<?php

use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

// ==============================================
// SEQUENTIAL STATUS TRANSITIONS
// ==============================================

test('can transition from PENDING to IN_PROGRESS', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create();
    $task = Task::factory()->forUser($user)->forProject($project)->pending()->create();

    actingAs($user);

    $response = $this->patch(route('tasks.updateStatus', $task), [
        'status' => TaskStatus::IN_PROGRESS->value,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Status atualizado com sucesso!');

    assertDatabaseHas('tasks', [
        'id' => $task->id,
        'status' => TaskStatus::IN_PROGRESS->value,
        'completed_at' => null,
    ]);
});

test('can transition from IN_PROGRESS to COMPLETED', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create();
    $task = Task::factory()->forUser($user)->forProject($project)->inProgress()->create();

    actingAs($user);

    $response = $this->patch(route('tasks.updateStatus', $task), [
        'status' => TaskStatus::COMPLETED->value,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Status atualizado com sucesso!');

    $task->refresh();

    expect($task->status)->toBe(TaskStatus::COMPLETED);
    expect($task->completed_at)->not->toBeNull();
});

test('cannot skip from PENDING to COMPLETED', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create();
    $task = Task::factory()->forUser($user)->forProject($project)->pending()->create();

    actingAs($user);

    $response = $this->patch(route('tasks.updateStatus', $task), [
        'status' => TaskStatus::COMPLETED->value,
    ]);

    $response->assertSessionHasErrors();

    $task->refresh();

    expect($task->status)->toBe(TaskStatus::PENDING);
    expect($task->completed_at)->toBeNull();
});

test('cannot transition from COMPLETED to any other status', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create();
    $task = Task::factory()->forUser($user)->forProject($project)->completed()->create();

    actingAs($user);

    $response = $this->patch(route('tasks.updateStatus', $task), [
        'status' => TaskStatus::PENDING->value,
    ]);

    $response->assertSessionHasErrors();

    $task->refresh();

    expect($task->status)->toBe(TaskStatus::COMPLETED);

    $response = $this->patch(route('tasks.updateStatus', $task), [
        'status' => TaskStatus::IN_PROGRESS->value,
    ]);

    $response->assertSessionHasErrors();

    $task->refresh();

    expect($task->status)->toBe(TaskStatus::COMPLETED);
});

test('cannot go backwards from IN_PROGRESS to PENDING', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create();
    $task = Task::factory()->forUser($user)->forProject($project)->inProgress()->create();

    actingAs($user);

    $response = $this->patch(route('tasks.updateStatus', $task), [
        'status' => TaskStatus::PENDING->value,
    ]);

    $response->assertSessionHasErrors();

    $task->refresh();

    expect($task->status)->toBe(TaskStatus::IN_PROGRESS);
});

// ==============================================
// COMPLETED_AT LOGIC
// ==============================================

test('completed_at is set when status changes to COMPLETED', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create();
    $task = Task::factory()->forUser($user)->forProject($project)->inProgress()->create();

    expect($task->completed_at)->toBeNull();

    actingAs($user);

    $this->patch(route('tasks.updateStatus', $task), [
        'status' => TaskStatus::COMPLETED->value,
    ]);

    $task->refresh();

    expect($task->completed_at)->not->toBeNull();
    expect($task->completed_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
});

test('completed_at is null for PENDING and IN_PROGRESS', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create();

    $pendingTask = Task::factory()->forUser($user)->forProject($project)->pending()->create();
    expect($pendingTask->completed_at)->toBeNull();

    $inProgressTask = Task::factory()->forUser($user)->forProject($project)->inProgress()->create();
    expect($inProgressTask->completed_at)->toBeNull();
});
