<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_tasks_page_renders_successfully(): void
    {
        $project = Project::factory()->create(['name' => 'Backend API']);
        $task = Task::factory()->create([
            'name' => 'Implement OAuth authentication',
            'project_id' => $project->id,
            'priority' => 1,
        ]);

        $response = $this->get(route('tasks.index'));

        $response->assertStatus(200);
        $response->assertSee('Taskboard');
        $response->assertSee('Backend API');
        $response->assertSee('Implement OAuth authentication');
        $response->assertSee('#1');
    }

    public function test_task_can_be_created_with_priority_and_timestamps(): void
    {
        $project = Project::factory()->create(['name' => 'Website']);

        $response = $this->post(route('tasks.store'), [
            'name' => 'Design new header component',
            'project_id' => $project->id,
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'name' => 'Design new header component',
            'project_id' => $project->id,
            'priority' => 1,
        ]);

        $task = Task::where('name', 'Design new header component')->first();
        $this->assertNotNull($task);
        $this->assertNotNull($task->created_at);
        $this->assertNotNull($task->updated_at);
    }

    public function test_creating_multiple_tasks_increments_priority(): void
    {
        $project = Project::factory()->create();

        $this->post(route('tasks.store'), [
            'name' => 'First Task',
            'project_id' => $project->id,
        ]);

        $this->post(route('tasks.store'), [
            'name' => 'Second Task',
            'project_id' => $project->id,
        ]);

        $first = Task::where('name', 'First Task')->first();
        $second = Task::where('name', 'Second Task')->first();

        $this->assertEquals(1, $first->priority);
        $this->assertEquals(2, $second->priority);
    }

    public function test_task_name_is_required(): void
    {
        $response = $this->post(route('tasks.store'), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_tasks_can_be_filtered_by_project_dropdown(): void
    {
        $projectA = Project::factory()->create(['name' => 'Project Alpha']);
        $projectB = Project::factory()->create(['name' => 'Project Beta']);

        $taskA = Task::factory()->create(['name' => 'Alpha Task', 'project_id' => $projectA->id, 'priority' => 1]);
        $taskB = Task::factory()->create(['name' => 'Beta Task', 'project_id' => $projectB->id, 'priority' => 1]);

        $response = $this->get(route('tasks.index', ['project' => $projectA->id]));

        $response->assertStatus(200);
        $response->assertSee('Alpha Task');
        $response->assertDontSee('Beta Task');
    }

    public function test_tasks_filter_by_unassigned(): void
    {
        $project = Project::factory()->create(['name' => 'Has Project']);
        $taskAssigned = Task::factory()->create(['name' => 'Assigned Task', 'project_id' => $project->id, 'priority' => 1]);
        $taskUnassigned = Task::factory()->create(['name' => 'Unassigned Task', 'project_id' => null, 'priority' => 1]);

        $response = $this->get(route('tasks.index', ['project' => 'none']));

        $response->assertStatus(200);
        $response->assertSee('Unassigned Task');
        $response->assertDontSee('Assigned Task');

        // Also verify 'unassigned' string parameter works identically
        $responseUnassigned = $this->get(route('tasks.index', ['project' => 'unassigned']));
        $responseUnassigned->assertStatus(200);
        $responseUnassigned->assertSee('Unassigned Task');
        $responseUnassigned->assertDontSee('Assigned Task');
    }

    public function test_task_can_be_updated(): void
    {
        $project1 = Project::factory()->create(['name' => 'Initial Project']);
        $project2 = Project::factory()->create(['name' => 'New Project']);

        $task = Task::factory()->create([
            'name' => 'Initial Task Name',
            'project_id' => $project1->id,
            'priority' => 1,
        ]);

        $response = $this->put(route('tasks.update', $task), [
            'name' => 'Updated Task Name',
            'project_id' => $project2->id,
            'priority' => 1,
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'name' => 'Updated Task Name',
            'project_id' => $project2->id,
        ]);
    }

    public function test_task_can_be_deleted(): void
    {
        $task = Task::factory()->create(['name' => 'Task to be deleted']);

        $response = $this->delete(route('tasks.destroy', $task));

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'is_active' => false,
        ]);

        $indexResponse = $this->get(route('tasks.index'));
        $indexResponse->assertDontSee('Task to be deleted');
    }

    public function test_tasks_reorder_via_drag_and_drop_endpoint_updates_priorities(): void
    {
        $task1 = Task::factory()->create(['name' => 'Task 1', 'priority' => 1]);
        $task2 = Task::factory()->create(['name' => 'Task 2', 'priority' => 2]);
        $task3 = Task::factory()->create(['name' => 'Task 3', 'priority' => 3]);

        // Drag Task 3 to top, then Task 1, then Task 2: [3, 1, 2]
        $response = $this->postJson(route('tasks.reorder'), [
            'task_ids' => [$task3->id, $task1->id, $task2->id],
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Task priorities updated successfully.',
            'count' => 3,
        ]);

        // Verify priorities in database: #1 is at top (task3), #2 is task1, #3 is task2
        $this->assertEquals(1, $task3->fresh()->priority);
        $this->assertEquals(2, $task1->fresh()->priority);
        $this->assertEquals(3, $task2->fresh()->priority);
    }

    public function test_project_can_be_created(): void
    {
        $response = $this->post(route('projects.store'), [
            'name' => 'Marketing Campaign',
        ]);

        $project = Project::where('name', 'Marketing Campaign')->first();
        $this->assertNotNull($project);

        $response->assertRedirect(route('tasks.index', ['project' => $project->id]));
        $this->assertDatabaseHas('projects', ['name' => 'Marketing Campaign']);
    }

    public function test_project_name_must_be_unique(): void
    {
        Project::factory()->create(['name' => 'Unique Project']);

        $response = $this->post(route('projects.store'), [
            'name' => 'Unique Project',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('projects', 1);
    }

    public function test_priority_recompacts_when_middle_task_is_deleted(): void
    {
        $project = Project::factory()->create();

        $task1 = Task::factory()->create(['name' => 'Task 1', 'project_id' => $project->id, 'priority' => 1]);
        $task2 = Task::factory()->create(['name' => 'Task 2', 'project_id' => $project->id, 'priority' => 2]);
        $task3 = Task::factory()->create(['name' => 'Task 3', 'project_id' => $project->id, 'priority' => 3]);
        $task4 = Task::factory()->create(['name' => 'Task 4', 'project_id' => $project->id, 'priority' => 4]);

        // Delete middle task #2
        $response = $this->delete(route('tasks.destroy', $task2));
        $response->assertRedirect(route('tasks.index'));

        // Verify middle task row is retained in database with is_active = 0
        $this->assertDatabaseHas('tasks', ['id' => $task2->id, 'is_active' => false]);
        $this->assertFalse($task2->fresh()->is_active);

        // Priorities of remaining active tasks in this project should be re-compacted: 1, 2, 3
        $this->assertEquals(1, $task1->fresh()->priority);
        $this->assertEquals(2, $task3->fresh()->priority);
        $this->assertEquals(3, $task4->fresh()->priority);
    }

    public function test_deleting_task_sets_is_active_to_zero_instead_of_deleting_row(): void
    {
        $task = Task::factory()->create([
            'name' => 'Soft Deactivated Task',
            'is_active' => true,
        ]);

        $response = $this->delete(route('tasks.destroy', $task));
        $response->assertRedirect(route('tasks.index'));

        // Verify task row is still in the database with is_active = 0
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'name' => 'Soft Deactivated Task',
            'is_active' => false,
        ]);

        $this->assertFalse($task->fresh()->is_active);

        // Verify it does not appear in active task listing
        $this->assertCount(0, Task::active()->get());
        $indexResponse = $this->get(route('tasks.index'));
        $indexResponse->assertDontSee('Soft Deactivated Task');
    }

    public function test_project_deletion_sets_task_project_id_to_null(): void
    {
        $project = Project::factory()->create(['name' => 'Temporary Project']);
        $task1 = Task::factory()->create(['name' => 'Task Alpha', 'project_id' => $project->id, 'priority' => 1]);
        $task2 = Task::factory()->create(['name' => 'Task Beta', 'project_id' => $project->id, 'priority' => 2]);

        $response = $this->delete(route('projects.destroy', $project));
        $response->assertRedirect(route('tasks.index'));

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);

        // Verify tasks are preserved and their project_id is set to null (nullOnDelete)
        $this->assertDatabaseHas('tasks', ['id' => $task1->id, 'project_id' => null]);
        $this->assertDatabaseHas('tasks', ['id' => $task2->id, 'project_id' => null]);

        $this->assertNull($task1->fresh()->project_id);
        $this->assertNull($task2->fresh()->project_id);
    }

    public function test_moving_task_to_another_project_assigns_next_priority_and_recompacts_source_project(): void
    {
        $projectA = Project::factory()->create(['name' => 'Project A']);
        $projectB = Project::factory()->create(['name' => 'Project B']);

        $taskA1 = Task::factory()->create(['name' => 'Task A1', 'project_id' => $projectA->id, 'priority' => 1]);
        $taskA2 = Task::factory()->create(['name' => 'Task A2', 'project_id' => $projectA->id, 'priority' => 2]);
        $taskA3 = Task::factory()->create(['name' => 'Task A3', 'project_id' => $projectA->id, 'priority' => 3]);

        $taskB1 = Task::factory()->create(['name' => 'Task B1', 'project_id' => $projectB->id, 'priority' => 1]);
        $taskB2 = Task::factory()->create(['name' => 'Task B2', 'project_id' => $projectB->id, 'priority' => 2]);

        // Move taskA2 from Project A to Project B
        $response = $this->put(route('tasks.update', $taskA2), [
            'name' => 'Task A2 Moved',
            'project_id' => $projectB->id,
        ]);

        $response->assertRedirect(route('tasks.index'));

        $taskA2Fresh = $taskA2->fresh();
        $this->assertEquals($projectB->id, $taskA2Fresh->project_id);
        // In Project B, previous max priority was 2, so next priority assigned should be 3
        $this->assertEquals(3, $taskA2Fresh->priority);

        // In Project A, priorities should be re-compacted to 1 and 2
        $this->assertEquals(1, $taskA1->fresh()->priority);
        $this->assertEquals(2, $taskA3->fresh()->priority);
    }

    public function test_tasks_reordering_with_arbitrary_sequence_and_exact_priorities(): void
    {
        $task1 = Task::factory()->create(['name' => 'First Task', 'priority' => 1]);
        $task2 = Task::factory()->create(['name' => 'Second Task', 'priority' => 2]);
        $task3 = Task::factory()->create(['name' => 'Third Task', 'priority' => 3]);
        $task4 = Task::factory()->create(['name' => 'Fourth Task', 'priority' => 4]);

        // Reorder with arbitrary sequence: 3, 4, 1, 2
        $response = $this->postJson(route('tasks.reorder'), [
            'task_ids' => [$task3->id, $task4->id, $task1->id, $task2->id],
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'count' => 4,
        ]);

        // Exact #1, #2, #3, #4 verification
        $this->assertEquals(1, $task3->fresh()->priority);
        $this->assertEquals(2, $task4->fresh()->priority);
        $this->assertEquals(3, $task1->fresh()->priority);
        $this->assertEquals(4, $task2->fresh()->priority);

        // Verify ordering query returns them in exact sequence
        $orderedNames = Task::ordered()->pluck('name')->all();
        $this->assertEquals(['Third Task', 'Fourth Task', 'First Task', 'Second Task'], $orderedNames);
    }

    public function test_task_store_fails_validation_with_invalid_project_id(): void
    {
        $response = $this->post(route('tasks.store'), [
            'name' => 'Task with invalid project',
            'project_id' => 999999,
        ]);

        $response->assertSessionHasErrors('project_id');
        $this->assertDatabaseMissing('tasks', ['name' => 'Task with invalid project']);
    }

    public function test_task_update_fails_validation_with_missing_name_or_invalid_project(): void
    {
        $project = Project::factory()->create();
        $task = Task::factory()->create(['name' => 'Original Name', 'project_id' => $project->id]);

        // Missing name
        $responseMissingName = $this->put(route('tasks.update', $task), [
            'name' => '',
            'project_id' => $project->id,
        ]);
        $responseMissingName->assertSessionHasErrors('name');

        // Invalid project_id
        $responseInvalidProject = $this->put(route('tasks.update', $task), [
            'name' => 'Valid Name',
            'project_id' => 888888,
        ]);
        $responseInvalidProject->assertSessionHasErrors('project_id');

        // Verify task remains untouched
        $this->assertEquals('Original Name', $task->fresh()->name);
        $this->assertEquals($project->id, $task->fresh()->project_id);
    }

    public function test_tasks_reorder_fails_validation_with_invalid_payload(): void
    {
        // 1. Empty task_ids array
        $responseEmpty = $this->postJson(route('tasks.reorder'), [
            'task_ids' => [],
        ]);
        $responseEmpty->assertStatus(422);
        $responseEmpty->assertJsonValidationErrors('task_ids');

        // 2. Non-existent task ID
        $responseInvalidId = $this->postJson(route('tasks.reorder'), [
            'task_ids' => [999999],
        ]);
        $responseInvalidId->assertStatus(422);
        $responseInvalidId->assertJsonValidationErrors('task_ids.0');

        // 3. Duplicate task IDs in the list
        $task = Task::factory()->create();
        $responseDuplicate = $this->postJson(route('tasks.reorder'), [
            'task_ids' => [$task->id, $task->id],
        ]);
        $responseDuplicate->assertStatus(422);
        $responseDuplicate->assertJsonValidationErrors('task_ids.0');
    }

    public function test_project_store_fails_validation_with_missing_or_too_long_name(): void
    {
        // Empty name
        $responseEmpty = $this->post(route('projects.store'), [
            'name' => '',
        ]);
        $responseEmpty->assertSessionHasErrors('name');

        // Name exceeding 120 characters
        $responseTooLong = $this->post(route('projects.store'), [
            'name' => str_repeat('a', 121),
        ]);
        $responseTooLong->assertSessionHasErrors('name');
    }

    public function test_priority_recompacts_when_first_task_is_deleted(): void
    {
        $project = Project::factory()->create();

        $task1 = Task::factory()->create(['name' => 'Task 1', 'project_id' => $project->id, 'priority' => 1]);
        $task2 = Task::factory()->create(['name' => 'Task 2', 'project_id' => $project->id, 'priority' => 2]);
        $task3 = Task::factory()->create(['name' => 'Task 3', 'project_id' => $project->id, 'priority' => 3]);

        // Delete the head task (#1)
        $response = $this->delete(route('tasks.destroy', $task1));
        $response->assertRedirect(route('tasks.index'));

        // Task 1 should be deactivated
        $this->assertFalse($task1->fresh()->is_active);

        // Remaining tasks should be re-compacted to 1 and 2
        $this->assertEquals(1, $task2->fresh()->priority);
        $this->assertEquals(2, $task3->fresh()->priority);
    }

    public function test_project_toggle_status_switches_active_flag(): void
    {
        $project = Project::factory()->create(['name' => 'Toggle Project', 'is_active' => true]);

        // Toggle to inactive
        $response = $this->patch(route('projects.toggle-status', $project));
        $response->assertRedirect();
        $this->assertFalse($project->fresh()->is_active);

        // Toggle back to active
        $response2 = $this->patch(route('projects.toggle-status', $project));
        $response2->assertRedirect();
        $this->assertTrue($project->fresh()->is_active);
    }

    public function test_moving_task_to_unassigned_assigns_next_priority_in_unassigned_pool(): void
    {
        $project = Project::factory()->create(['name' => 'Source Project']);

        $taskAssigned1 = Task::factory()->create(['name' => 'Project Task 1', 'project_id' => $project->id, 'priority' => 1]);
        $taskAssigned2 = Task::factory()->create(['name' => 'Project Task 2', 'project_id' => $project->id, 'priority' => 2]);

        $taskUnassigned1 = Task::factory()->create(['name' => 'General Task 1', 'project_id' => null, 'priority' => 1]);

        // Move taskAssigned1 to unassigned (project_id = null)
        $response = $this->put(route('tasks.update', $taskAssigned1), [
            'name' => 'Moved to General',
            'project_id' => '',
        ]);

        $response->assertRedirect(route('tasks.index'));

        $freshAssigned1 = $taskAssigned1->fresh();
        $this->assertNull($freshAssigned1->project_id);
        // Next unassigned priority was 2
        $this->assertEquals(2, $freshAssigned1->priority);

        // Source project remaining task should recompact to priority 1
        $this->assertEquals(1, $taskAssigned2->fresh()->priority);
    }

    public function test_creating_unassigned_tasks_increments_consecutive_priorities(): void
    {
        $this->post(route('tasks.store'), [
            'name' => 'Unassigned Alpha',
            'project_id' => '',
        ]);

        $this->post(route('tasks.store'), [
            'name' => 'Unassigned Beta',
            'project_id' => '',
        ]);

        $taskA = Task::where('name', 'Unassigned Alpha')->first();
        $taskB = Task::where('name', 'Unassigned Beta')->first();

        $this->assertNull($taskA->project_id);
        $this->assertNull($taskB->project_id);
        $this->assertEquals(1, $taskA->priority);
        $this->assertEquals(2, $taskB->priority);
    }

    public function test_dashboard_statistics_computed_accurately(): void
    {
        // 2 active tasks with priority 1 (different projects or unassigned)
        $projA = Project::factory()->create();
        $projB = Project::factory()->create();

        Task::factory()->create(['name' => 'High 1', 'project_id' => $projA->id, 'priority' => 1, 'is_active' => true]);
        Task::factory()->create(['name' => 'High 2', 'project_id' => $projB->id, 'priority' => 1, 'is_active' => true]);
        Task::factory()->create(['name' => 'In Progress 1', 'project_id' => $projA->id, 'priority' => 2, 'is_active' => true]);
        Task::factory()->create(['name' => 'In Progress 2', 'project_id' => $projA->id, 'priority' => 3, 'is_active' => true]);
        Task::factory()->create(['name' => 'Completed Task 1', 'project_id' => $projA->id, 'priority' => 1, 'is_active' => false]);
        Task::factory()->create(['name' => 'Completed Task 2', 'project_id' => $projB->id, 'priority' => 2, 'is_active' => false]);

        $response = $this->get(route('tasks.index'));
        $response->assertStatus(200);

        $response->assertViewHas('stats', [
            'total' => 4,          // 4 active
            'completed' => 2,      // 2 is_active = false
            'in_progress' => 2,    // priority > 1 (priority 2 and 3)
            'high_priority' => 2,  // priority == 1
        ]);
    }

    public function test_task_priority_can_be_updated_within_same_project(): void
    {
        $project = Project::factory()->create(['is_active' => true]);

        $task1 = Task::factory()->create(['name' => 'Task 1', 'project_id' => $project->id, 'priority' => 1]);
        $task2 = Task::factory()->create(['name' => 'Task 2', 'project_id' => $project->id, 'priority' => 2]);
        $task3 = Task::factory()->create(['name' => 'Task 3', 'project_id' => $project->id, 'priority' => 3]);

        // Move Task 3 from priority 3 to priority 1
        $response = $this->put(route('tasks.update', $task3), [
            'name' => 'Task 3 Promoted',
            'project_id' => $project->id,
            'priority' => 1,
        ]);

        $response->assertRedirect(route('tasks.index'));

        // Task 3 is now #1, Task 1 shifted to #2, Task 2 shifted to #3
        $this->assertEquals(1, $task3->fresh()->priority);
        $this->assertEquals(2, $task1->fresh()->priority);
        $this->assertEquals(3, $task2->fresh()->priority);
    }

    public function test_cannot_modify_tasks_belonging_to_inactive_project(): void
    {
        $inactiveProject = Project::factory()->create(['name' => 'Frozen Project', 'is_active' => false]);
        $task = Task::factory()->create(['name' => 'Frozen Task', 'project_id' => $inactiveProject->id, 'priority' => 1]);

        // Attempt to update task name and priority in an inactive project
        $response = $this->put(route('tasks.update', $task), [
            'name' => 'Attempted Edit',
            'project_id' => $inactiveProject->id,
            'priority' => 2,
        ]);

        $response->assertSessionHasErrors('project_id');
        $this->assertEquals('Frozen Task', $task->fresh()->name);
        $this->assertEquals(1, $task->fresh()->priority);

        // Attempt to store task in an inactive project
        $storeResponse = $this->post(route('tasks.store'), [
            'name' => 'Disallowed Task',
            'project_id' => $inactiveProject->id,
        ]);

        $storeResponse->assertSessionHasErrors('project_id');
        $this->assertDatabaseMissing('tasks', ['name' => 'Disallowed Task']);
    }

    public function test_cannot_delete_tasks_belonging_to_inactive_project(): void
    {
        $inactiveProject = Project::factory()->create(['name' => 'Locked Project', 'is_active' => false]);
        $task = Task::factory()->create(['name' => 'Locked Task', 'project_id' => $inactiveProject->id, 'priority' => 1]);

        $response = $this->delete(route('tasks.destroy', $task));

        $response->assertSessionHasErrors('project_id');
        $this->assertTrue($task->fresh()->is_active);
    }

    public function test_cannot_reorder_tasks_belonging_to_inactive_project(): void
    {
        $inactiveProject = Project::factory()->create(['name' => 'Locked Reorder Project', 'is_active' => false]);
        $task1 = Task::factory()->create(['name' => 'Locked 1', 'project_id' => $inactiveProject->id, 'priority' => 1]);
        $task2 = Task::factory()->create(['name' => 'Locked 2', 'project_id' => $inactiveProject->id, 'priority' => 2]);

        $response = $this->postJson(route('tasks.reorder'), [
            'task_ids' => [$task2->id, $task1->id],
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'status' => 'error',
        ]);
        $this->assertEquals(1, $task1->fresh()->priority);
        $this->assertEquals(2, $task2->fresh()->priority);
    }

    public function test_priority_recompacts_when_last_task_is_deleted(): void
    {
        $project = Project::factory()->create();

        $task1 = Task::factory()->create(['name' => 'Task A', 'project_id' => $project->id, 'priority' => 1]);
        $task2 = Task::factory()->create(['name' => 'Task B', 'project_id' => $project->id, 'priority' => 2]);
        $task3 = Task::factory()->create(['name' => 'Task C', 'project_id' => $project->id, 'priority' => 3]);

        $response = $this->delete(route('tasks.destroy', $task3));
        $response->assertRedirect(route('tasks.index'));

        $this->assertFalse($task3->fresh()->is_active);
        $this->assertEquals(1, $task1->fresh()->priority);
        $this->assertEquals(2, $task2->fresh()->priority);
    }

    public function test_deleting_multiple_tasks_maintains_contiguous_priority_sequence(): void
    {
        $project = Project::factory()->create();

        $tasks = [];
        for ($i = 1; $i <= 5; $i++) {
            $tasks[$i] = Task::factory()->create([
                'name' => "Batch Task $i",
                'project_id' => $project->id,
                'priority' => $i,
            ]);
        }

        // Delete #2 and #4
        $this->delete(route('tasks.destroy', $tasks[2]));
        $this->delete(route('tasks.destroy', $tasks[4]));

        // Remaining active tasks (originally #1, #3, #5) should now be 1, 2, 3
        $this->assertEquals(1, $tasks[1]->fresh()->priority);
        $this->assertEquals(2, $tasks[3]->fresh()->priority);
        $this->assertEquals(3, $tasks[5]->fresh()->priority);

        $activePriorities = Task::active()->forProject($project->id)->ordered()->pluck('priority')->all();
        $this->assertEquals([1, 2, 3], $activePriorities);
    }
}

