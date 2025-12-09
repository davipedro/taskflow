<?php

use App\Enums\TaskPriority;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

use function Pest\Laravel\actingAs;

// ==============================================
// TASK POLICY AUTHORIZATION TESTS
// ==============================================

test('user can view task from their project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create();
    $task = Task::factory()->forUser($user)->forProject($project)->create();

    actingAs($user);

    $response = $this->get(route('tasks.show', $task));

    $response->assertSuccessful();
});

test('user cannot view task from another user project', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $otherProject = Project::factory()->forUser($otherUser)->create();
    $otherTask = Task::factory()->forUser($otherUser)->forProject($otherProject)->create();

    actingAs($user);

    $response = $this->get(route('tasks.show', $otherTask));

    $response->assertForbidden();
});

test('user can create task in their project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create();

    actingAs($user);

    $response = $this->post(route('projects.tasks.store', $project), [
        'title' => 'New Task',
        'priority' => TaskPriority::MEDIUM->value,
        'project_id' => $project->id,
        'user_id' => $user->id,
    ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();
});

test('user cannot create task in another user project', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $otherProject = Project::factory()->forUser($otherUser)->create();

    actingAs($user);

    $response = $this->post(route('projects.tasks.store', $otherProject), [
        'title' => 'Unauthorized Task',
        'priority' => TaskPriority::MEDIUM->value,
        'project_id' => $otherProject->id,
        'user_id' => $user->id,
    ]);

    $response->assertForbidden();
});

test('user can update task from their project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create();
    $task = Task::factory()->forUser($user)->forProject($project)->create();

    actingAs($user);

    $response = $this->put(route('tasks.update', $task), [
        'title' => 'Updated Task',
        'priority' => TaskPriority::HIGH->value,
    ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();
});

test('user cannot update task from another user project', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $otherProject = Project::factory()->forUser($otherUser)->create();
    $otherTask = Task::factory()->forUser($otherUser)->forProject($otherProject)->create();

    actingAs($user);

    $response = $this->put(route('tasks.update', $otherTask), [
        'title' => 'Hacked Task',
        'priority' => TaskPriority::HIGH->value,
    ]);

    $response->assertForbidden();
});

test('user can delete task from their project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create();
    $task = Task::factory()->forUser($user)->forProject($project)->create();

    actingAs($user);

    $response = $this->delete(route('tasks.destroy', $task));

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();
});

test('user cannot delete task from another user project', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $otherProject = Project::factory()->forUser($otherUser)->create();
    $otherTask = Task::factory()->forUser($otherUser)->forProject($otherProject)->create();

    actingAs($user);

    $response = $this->delete(route('tasks.destroy', $otherTask));

    $response->assertForbidden();
});
