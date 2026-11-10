<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReorderTasksRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TaskController extends Controller
{
    /**
     * Display a listing of tasks, optionally filtered by project.
     */
    public function index(Request $request): View
    {
        $projects = Project::query()
            ->withCount(['tasks' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get();

        $selectedProject = $request->query('project');

        $query = Task::query()
            ->active()
            ->with('project')
            ->ordered();

        if ($selectedProject !== null && $selectedProject !== '') {
            $isUnassigned = in_array(strtolower((string) $selectedProject), ['none', 'unassigned', 'null', '0', 'no-project'], true);
            if ($isUnassigned) {
                $query->where(function ($q) {
                    $q->whereNull('project_id')->orWhere('project_id', 0);
                });
            } else {
                $query->where('project_id', (int) $selectedProject);
            }
        }

        $tasks = $query->get();

        $unassignedCount = Task::active()
            ->where(function ($q) {
                $q->whereNull('project_id')->orWhere('project_id', 0);
            })
            ->count();

        $totalActiveTasks = Task::active()->count();

        $stats = [
            'total' => $totalActiveTasks,
            'completed' => Task::where('is_active', false)->count(),
            'in_progress' => Task::active()->where('priority', '>', 1)->count(),
            'high_priority' => Task::active()->where('priority', 1)->count(),
        ];

        return view('tasks.index', compact('tasks', 'projects', 'selectedProject', 'stats', 'unassignedCount', 'totalActiveTasks'));
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $projectId = !empty($validated['project_id']) ? (int) $validated['project_id'] : null;

        if ($projectId !== null) {
            $project = Project::find($projectId);
            if ($project && ! $project->is_active) {
                return back()->withErrors(['project_id' => sprintf('Project "%s" is inactive. Cannot add tasks to an inactive project.', $project->name)])->withInput();
            }
        }

        DB::transaction(function () use ($validated, $projectId) {
            $priority = !empty($validated['priority'])
                ? (int) $validated['priority']
                : Task::nextPriority($projectId);

            Task::create([
                'name' => $validated['name'],
                'project_id' => $projectId,
                'priority' => $priority,
            ]);

            $this->compactPriorities($projectId);
        });

        $filter = $this->buildFilterQuery($request, $projectId);

        return redirect()->route('tasks.index', $filter)->with('success', 'Task created successfully.');
    }

    /**
     * Update the specified task in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $validated = $request->validated();
        $newProjectId = !empty($validated['project_id']) ? (int) $validated['project_id'] : null;
        $oldProjectId = $task->project_id;

        // Disallow modifying task if its current project is inactive
        if ($task->project && ! $task->project->is_active) {
            return back()->withErrors(['project_id' => sprintf('Project "%s" is inactive. Tasks belonging to an inactive project cannot be modified. Please reactivate the project first.', $task->project->name)]);
        }

        // Disallow moving task to an inactive project
        if ($newProjectId !== null && $newProjectId !== $oldProjectId) {
            $targetProject = Project::find($newProjectId);
            if ($targetProject && ! $targetProject->is_active) {
                return back()->withErrors(['project_id' => sprintf('Project "%s" is inactive. Cannot move task to an inactive project.', $targetProject->name)]);
            }
        }

        DB::transaction(function () use ($task, $validated, $oldProjectId, $newProjectId) {
            $task->name = $validated['name'];

            if ($oldProjectId !== $newProjectId) {
                // Moving to another project: assign next priority in target project
                $task->project_id = $newProjectId;
                $task->priority = Task::nextPriority($newProjectId);
                $task->save();

                // Recompact old project priorities
                $this->compactPriorities($oldProjectId);
            } else {
                // Same project: handle priority update properly with shifting
                $newPriority = !empty($validated['priority']) ? max(1, (int) $validated['priority']) : $task->priority;
                $oldPriority = $task->priority;

                if ($newPriority !== $oldPriority) {
                    if ($newPriority < $oldPriority) {
                        // Moved UP in priority (e.g. from 3 to 1)
                        // Tasks between newPriority and oldPriority - 1 shift DOWN (+1)
                        Task::active()
                            ->forProject($oldProjectId)
                            ->where('id', '!=', $task->id)
                            ->whereBetween('priority', [$newPriority, $oldPriority - 1])
                            ->increment('priority');
                    } else {
                        // Moved DOWN in priority (e.g. from 1 to 3)
                        // Tasks between oldPriority + 1 and newPriority shift UP (-1)
                        Task::active()
                            ->forProject($oldProjectId)
                            ->where('id', '!=', $task->id)
                            ->whereBetween('priority', [$oldPriority + 1, $newPriority])
                            ->decrement('priority');
                    }
                    $task->priority = $newPriority;
                }

                $task->save();
            }
        });

        $filter = [];
        if ($request->filled('filter_project')) {
            $filter['project'] = $request->input('filter_project');
        }

        return redirect()->route('tasks.index', $filter)->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Request $request, Task $task): RedirectResponse
    {
        if ($task->project && ! $task->project->is_active) {
            return back()->withErrors(['project_id' => sprintf('Project "%s" is inactive. Cannot delete tasks from an inactive project.', $task->project->name)]);
        }

        $projectId = $task->project_id;

        DB::transaction(function () use ($task, $projectId) {
            $task->update(['is_active' => false]);
            $this->compactPriorities($projectId);
        });

        $filter = [];
        if ($request->filled('filter_project')) {
            $filter['project'] = $request->input('filter_project');
        }

        return redirect()->route('tasks.index', $filter)->with('success', 'Task deleted successfully.');
    }

    /**
     * Reorder tasks using the provided ordered list of IDs (from drag-and-drop).
     */
    public function reorder(ReorderTasksRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $taskIds = $validated['task_ids'];

        // Disallow reordering if any task belongs to an inactive project
        $hasInactiveProject = Task::whereIn('id', $taskIds)
            ->whereHas('project', fn ($q) => $q->where('is_active', false))
            ->exists();

        if ($hasInactiveProject) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot reorder tasks in an inactive project. Please reactivate the project first.',
            ], 422);
        }

        DB::transaction(function () use ($taskIds) {
            foreach ($taskIds as $index => $id) {
                Task::where('id', $id)->update([
                    'priority' => $index + 1,
                ]);
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Task priorities updated successfully.',
            'count' => count($taskIds),
        ]);
    }

    /**
     * Compact priorities so they are strictly sequential: 1, 2, 3...
     */
    private function compactPriorities(?int $projectId): void
    {
        $tasks = Task::query()
            ->active()
            ->forProject($projectId)
            ->ordered()
            ->get();

        foreach ($tasks as $index => $task) {
            $expected = $index + 1;
            if ($task->priority !== $expected) {
                $task->update(['priority' => $expected]);
            }
        }
    }

    /**
     * Determine redirect filter parameters.
     */
    private function buildFilterQuery(Request $request, ?int $projectId): array
    {
        if ($request->filled('filter_project')) {
            if ($projectId !== null) {
                return ['project' => $projectId];
            }

            if ($request->input('filter_project') === 'none') {
                return ['project' => 'none'];
            }

            return [];
        }

        return [];
    }
}
