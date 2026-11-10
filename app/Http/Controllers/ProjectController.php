<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;

class ProjectController extends Controller
{
    /**
     * Store a newly created project in storage.
     */
    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $project = Project::create($validated);

        return redirect()
            ->route('tasks.index', ['project' => $project->id])
            ->with('success', sprintf('Project "%s" created successfully.', $project->name));
    }

    /**
     * Toggle the active / inactive status of the specified project.
     */
    public function toggleStatus(Project $project): RedirectResponse
    {
        $project->is_active = ! $project->is_active;
        $project->save();

        $status = $project->is_active ? 'active' : 'inactive';

        return back()->with('success', sprintf('Project "%s" is now %s.', $project->name, $status));
    }

    /**
     * Remove the specified project from storage.
     */
    public function destroy(Project $project): RedirectResponse
    {
        $projectName = $project->name;
        $project->delete();

        return redirect()
            ->route('tasks.index')
            ->with('success', sprintf('Project "%s" deleted. Associated tasks are now unassigned.', $projectName));
    }
}
