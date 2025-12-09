<?php

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertSoftDeleted;

// ==============================================
// GUEST ACCESS TESTS
// ==============================================

test('guest cannot access tasks index', function () {
    $response = $this->get(route('tasks.index'));

    $response->assertRedirect(route('login'));
});

test('guest cannot create task', function () {
    $project = Project::factory()->create();

    $response = $this->post(route('projects.tasks.store', $project), [
        'title' => 'Test Task',
        'priority' => TaskPriority::MEDIUM->value,
    ]);

    $response->assertRedirect(route('login'));
});

test('guest cannot view task', function () {
    $task = Task::factory()->create();

    $response = $this->get(route('tasks.show', $task));

    $response->assertRedirect(route('login'));
});

test('guest cannot update task', function () {
    $task = Task::factory()->create();

    $response = $this->put(route('tasks.update', $task), [
        'title' => 'Updated Title',
    ]);

    $response->assertRedirect(route('login'));
});

test('guest cannot delete task', function () {
    $task = Task::factory()->create();

    $response = $this->delete(route('tasks.destroy', $task));

    $response->assertRedirect(route('login'));
});

// ==============================================
// INDEX TESTS
// ==============================================

test('authenticated user can see their tasks list', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create();

    Task::factory()->count(3)->forUser($user)->forProject($project)->create();

    actingAs($user);

    $response = $this->get(route('tasks.index'));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Tasks/Index')
        ->has('tasks.data', 3)
    );
});

test('user only sees their own tasks', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $userProject = Project::factory()->forUser($user)->create();
    $otherProject = Project::factory()->forUser($otherUser)->create();

    Task::factory()->forUser($user)->forProject($userProject)->create(['title' => 'My Task']);
    Task::factory()->forUser($otherUser)->forProject($otherProject)->create(['title' => 'Other Task']);

    actingAs($user);

    $response = $this->get(route('tasks.index'));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Tasks/Index')
        ->has('tasks.data', 1)
        ->where('tasks.data.0.title', 'My Task')
    );
});

test('tasks are paginated correctly', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create();

    Task::factory()->count(10)->forUser($user)->forProject($project)->create();

    actingAs($user);

    $response = $this->get(route('tasks.index'));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Tasks/Index')
        ->has('tasks.data', 6)
        ->has('tasks.links')
    );
});

test('tasks are ordered by created_at desc by default', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create();

    Task::factory()->forUser($user)->forProject($project)->create(['title' => 'First', 'created_at' => now()->subDays(2)]);
    Task::factory()->forUser($user)->forProject($project)->create(['title' => 'Second', 'created_at' => now()->subDay()]);
    Task::factory()->forUser($user)->forProject($project)->create(['title' => 'Third', 'created_at' => now()]);

    actingAs($user);

    $response = $this->get(route('tasks.index'));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Tasks/Index')
        ->where('tasks.data.0.title', 'Third')
        ->where('tasks.data.1.title', 'Second')
        ->where('tasks.data.2.title', 'First')
    );
});

// ==============================================
// STORE (CREATE) TESTS
// ==============================================

test('authenticated user can create task with valid data', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create();

    actingAs($user);

    $response = $this->post(route('projects.tasks.store', $project), [
        'title' => 'New Task',
        'description' => 'Task description',
        'priority' => TaskPriority::HIGH->value,
        'deadline' => now()->addDays(7)->format('Y-m-d'),
        'project_id' => $project->id,
        'user_id' => $user->id,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('message', 'Tarefa criada com sucesso!');

    assertDatabaseHas('tasks', [
        'title' => 'New Task',
        'description' => 'Task description',
        'priority' => TaskPriority::HIGH->value,
        'status' => TaskStatus::PENDING->value,
        'user_id' => $user->id,
        'project_id' => $project->id,
    ]);
});

test('task is associated with authenticated user and project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create();

    actingAs($user);

    $this->post(route('projects.tasks.store', $project), [
        'title' => 'Associated Task',
        'priority' => TaskPriority::MEDIUM->value,
        'project_id' => $project->id,
        'user_id' => $user->id,
    ]);

    $task = Task::where('title', 'Associated Task')->first();

    expect($task->user_id)->toBe($user->id);
    expect($task->project_id)->toBe($project->id);
});

test('task title is required', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create();

    actingAs($user);

    $response = $this->post(route('projects.tasks.store', $project), [
        'priority' => TaskPriority::MEDIUM->value,
        'project_id' => $project->id,
        'user_id' => $user->id,
    ]);

    $response->assertSessionHasErrors('title');
});

test('task priority is required and must be valid enum', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create();

    actingAs($user);

    $response = $this->post(route('projects.tasks.store', $project), [
        'title' => 'Test Task',
        'project_id' => $project->id,
        'user_id' => $user->id,
    ]);

    $response->assertSessionHasErrors('priority');

    $response = $this->post(route('projects.tasks.store', $project), [
        'title' => 'Test Task',
        'priority' => 'INVALID',
        'project_id' => $project->id,
        'user_id' => $user->id,
    ]);

    $response->assertSessionHasErrors('priority');
});

test('task deadline is optional and must be valid date', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create();

    actingAs($user);

    $response = $this->post(route('projects.tasks.store', $project), [
        'title' => 'Task without deadline',
        'priority' => TaskPriority::LOW->value,
        'project_id' => $project->id,
        'user_id' => $user->id,
    ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();

    $response = $this->post(route('projects.tasks.store', $project), [
        'title' => 'Task with invalid deadline',
        'priority' => TaskPriority::LOW->value,
        'deadline' => 'invalid-date',
        'project_id' => $project->id,
        'user_id' => $user->id,
    ]);

    $response->assertSessionHasErrors('deadline');
});

// ==============================================
// SHOW/UPDATE/DELETE AUTHORIZATION TESTS
// ==============================================

test('user can view their own task', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create();
    $task = Task::factory()->forUser($user)->forProject($project)->create();

    actingAs($user);

    $response = $this->get(route('tasks.show', $task));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Tasks/Show')
        ->has('task')
    );
});

test('user cannot view another user task', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $otherProject = Project::factory()->forUser($otherUser)->create();
    $otherTask = Task::factory()->forUser($otherUser)->forProject($otherProject)->create();

    actingAs($user);

    $response = $this->get(route('tasks.show', $otherTask));

    $response->assertForbidden();
});

test('user can update their own task', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create();
    $task = Task::factory()->forUser($user)->forProject($project)->create(['title' => 'Original Title']);

    actingAs($user);

    $response = $this->put(route('tasks.update', $task), [
        'title' => 'Updated Title',
        'description' => 'Updated description',
        'priority' => TaskPriority::HIGH->value,
        'deadline' => now()->addDays(10)->format('Y-m-d'),
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Tarefa atualizada com sucesso!');

    assertDatabaseHas('tasks', [
        'id' => $task->id,
        'title' => 'Updated Title',
        'description' => 'Updated description',
        'priority' => TaskPriority::HIGH->value,
    ]);
});

test('user cannot update another user task', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $otherProject = Project::factory()->forUser($otherUser)->create();
    $otherTask = Task::factory()->forUser($otherUser)->forProject($otherProject)->create();

    actingAs($user);

    $response = $this->put(route('tasks.update', $otherTask), [
        'title' => 'Hacked Title',
        'priority' => TaskPriority::HIGH->value,
    ]);

    $response->assertForbidden();
});

test('user can delete their own task', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create();
    $task = Task::factory()->forUser($user)->forProject($project)->create();

    actingAs($user);

    $response = $this->delete(route('tasks.destroy', $task));

    $response->assertRedirect();
    $response->assertSessionHas('message', 'Tarefa deletada com sucesso!');

    assertSoftDeleted('tasks', [
        'id' => $task->id,
    ]);
});

test('user cannot delete another user task', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $otherProject = Project::factory()->forUser($otherUser)->create();
    $otherTask = Task::factory()->forUser($otherUser)->forProject($otherProject)->create();

    actingAs($user);

    $response = $this->delete(route('tasks.destroy', $otherTask));

    $response->assertForbidden();
});
