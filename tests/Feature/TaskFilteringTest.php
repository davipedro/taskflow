<?php

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

use function Pest\Laravel\actingAs;

// ==============================================
// STATUS FILTER TESTS
// ==============================================

test('can filter tasks by status PENDING', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create();

    Task::factory()->forUser($user)->forProject($project)->pending()->create(['title' => 'Pending Task']);
    Task::factory()->forUser($user)->forProject($project)->inProgress()->create(['title' => 'In Progress Task']);
    Task::factory()->forUser($user)->forProject($project)->completed()->create(['title' => 'Completed Task']);

    actingAs($user);

    $response = $this->get(route('tasks.index', ['status' => TaskStatus::PENDING->value]));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Tasks/Index')
        ->has('tasks.data', 1)
        ->where('tasks.data.0.status', TaskStatus::PENDING->value)
    );
});

test('can filter tasks by status IN_PROGRESS', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create();

    Task::factory()->forUser($user)->forProject($project)->pending()->create();
    Task::factory()->forUser($user)->forProject($project)->inProgress()->count(2)->create();

    actingAs($user);

    $response = $this->get(route('tasks.index', ['status' => TaskStatus::IN_PROGRESS->value]));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Tasks/Index')
        ->has('tasks.data', 2)
        ->where('tasks.data.0.status', TaskStatus::IN_PROGRESS->value)
    );
});

test('can filter tasks by status COMPLETED', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create();

    Task::factory()->forUser($user)->forProject($project)->pending()->create();
    Task::factory()->forUser($user)->forProject($project)->completed()->count(3)->create();

    actingAs($user);

    $response = $this->get(route('tasks.index', ['status' => TaskStatus::COMPLETED->value]));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Tasks/Index')
        ->has('tasks.data', 3)
        ->where('tasks.data.0.status', TaskStatus::COMPLETED->value)
    );
});

// ==============================================
// PRIORITY FILTER TESTS
// ==============================================

test('can filter tasks by priority HIGH', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create();

    Task::factory()->forUser($user)->forProject($project)->highPriority()->count(2)->create();
    Task::factory()->forUser($user)->forProject($project)->mediumPriority()->create();
    Task::factory()->forUser($user)->forProject($project)->lowPriority()->create();

    actingAs($user);

    $response = $this->get(route('tasks.index', ['priority' => TaskPriority::HIGH->value]));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Tasks/Index')
        ->has('tasks.data', 2)
        ->where('tasks.data.0.priority', TaskPriority::HIGH->value)
    );
});

test('can filter tasks by priority MEDIUM', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create();

    Task::factory()->forUser($user)->forProject($project)->highPriority()->create();
    Task::factory()->forUser($user)->forProject($project)->mediumPriority()->count(3)->create();
    Task::factory()->forUser($user)->forProject($project)->lowPriority()->create();

    actingAs($user);

    $response = $this->get(route('tasks.index', ['priority' => TaskPriority::MEDIUM->value]));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Tasks/Index')
        ->has('tasks.data', 3)
        ->where('tasks.data.0.priority', TaskPriority::MEDIUM->value)
    );
});

test('can filter tasks by priority LOW', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create();

    Task::factory()->forUser($user)->forProject($project)->highPriority()->create();
    Task::factory()->forUser($user)->forProject($project)->lowPriority()->count(2)->create();

    actingAs($user);

    $response = $this->get(route('tasks.index', ['priority' => TaskPriority::LOW->value]));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Tasks/Index')
        ->has('tasks.data', 2)
        ->where('tasks.data.0.priority', TaskPriority::LOW->value)
    );
});

// ==============================================
// SORTING TESTS
// ==============================================

test('can sort tasks by created_at ascending', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create();

    Task::factory()->forUser($user)->forProject($project)->create(['title' => 'Third', 'created_at' => now()]);
    Task::factory()->forUser($user)->forProject($project)->create(['title' => 'First', 'created_at' => now()->subDays(2)]);
    Task::factory()->forUser($user)->forProject($project)->create(['title' => 'Second', 'created_at' => now()->subDay()]);

    actingAs($user);

    $response = $this->get(route('tasks.index', ['sort_by' => 'created_at', 'sort_order' => 'asc']));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Tasks/Index')
        ->where('tasks.data.0.title', 'First')
        ->where('tasks.data.1.title', 'Second')
        ->where('tasks.data.2.title', 'Third')
    );
});

test('can sort tasks by created_at descending', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create();

    Task::factory()->forUser($user)->forProject($project)->create(['title' => 'First', 'created_at' => now()->subDays(2)]);
    Task::factory()->forUser($user)->forProject($project)->create(['title' => 'Second', 'created_at' => now()->subDay()]);
    Task::factory()->forUser($user)->forProject($project)->create(['title' => 'Third', 'created_at' => now()]);

    actingAs($user);

    $response = $this->get(route('tasks.index', ['sort_by' => 'created_at', 'sort_order' => 'desc']));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Tasks/Index')
        ->where('tasks.data.0.title', 'Third')
        ->where('tasks.data.1.title', 'Second')
        ->where('tasks.data.2.title', 'First')
    );
});

test('can sort tasks by deadline', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create();

    Task::factory()->forUser($user)->forProject($project)->create(['title' => 'Far', 'deadline' => now()->addDays(10)]);
    Task::factory()->forUser($user)->forProject($project)->create(['title' => 'Near', 'deadline' => now()->addDays(2)]);
    Task::factory()->forUser($user)->forProject($project)->create(['title' => 'Medium', 'deadline' => now()->addDays(5)]);

    actingAs($user);

    $response = $this->get(route('tasks.index', ['sort_by' => 'deadline', 'sort_order' => 'asc']));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Tasks/Index')
        ->where('tasks.data.0.title', 'Near')
        ->where('tasks.data.1.title', 'Medium')
        ->where('tasks.data.2.title', 'Far')
    );
});

test('can sort tasks by priority', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create();

    Task::factory()->forUser($user)->forProject($project)->lowPriority()->create(['title' => 'Low Task']);
    Task::factory()->forUser($user)->forProject($project)->highPriority()->create(['title' => 'High Task']);
    Task::factory()->forUser($user)->forProject($project)->mediumPriority()->create(['title' => 'Medium Task']);

    actingAs($user);

    $response = $this->get(route('tasks.index', ['sort_by' => 'priority', 'sort_order' => 'desc']));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Tasks/Index')
        ->has('tasks.data', 3)
    );
});

// ==============================================
// COMBINED FILTERS TESTS
// ==============================================

test('can filter by status and priority together', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create();

    Task::factory()->forUser($user)->forProject($project)->pending()->highPriority()->create(['title' => 'Match']);
    Task::factory()->forUser($user)->forProject($project)->pending()->lowPriority()->create(['title' => 'Wrong Priority']);
    Task::factory()->forUser($user)->forProject($project)->completed()->highPriority()->create(['title' => 'Wrong Status']);

    actingAs($user);

    $response = $this->get(route('tasks.index', [
        'status' => TaskStatus::PENDING->value,
        'priority' => TaskPriority::HIGH->value,
    ]));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Tasks/Index')
        ->has('tasks.data', 1)
        ->where('tasks.data.0.title', 'Match')
    );
});

test('filters and sorting work together correctly', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create();

    Task::factory()->forUser($user)->forProject($project)->pending()->create(['title' => 'Second', 'created_at' => now()->subDay()]);
    Task::factory()->forUser($user)->forProject($project)->pending()->create(['title' => 'First', 'created_at' => now()->subDays(2)]);
    Task::factory()->forUser($user)->forProject($project)->completed()->create(['title' => 'Excluded', 'created_at' => now()]);

    actingAs($user);

    $response = $this->get(route('tasks.index', [
        'status' => TaskStatus::PENDING->value,
        'sort_by' => 'created_at',
        'sort_order' => 'asc',
    ]));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Tasks/Index')
        ->has('tasks.data', 2)
        ->where('tasks.data.0.title', 'First')
        ->where('tasks.data.1.title', 'Second')
    );
});
