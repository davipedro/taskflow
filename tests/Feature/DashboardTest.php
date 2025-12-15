<?php

use App\Models\Task;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertStatus(200);
});

test('dashboard displays correct task statistics', function () {
    $user = User::factory()->create();

    Task::factory()->count(3)->create(['user_id' => $user->id, 'status' => 'PENDING']);
    Task::factory()->count(2)->create(['user_id' => $user->id, 'status' => 'IN_PROGRESS']);
    Task::factory()->count(1)->create(['user_id' => $user->id, 'status' => 'COMPLETED']);

    $this->actingAs($user);

    $response = $this->get(route('dashboard'));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Dashboard')
        ->has('stats')
        ->where('stats.pending', 3)
        ->where('stats.in_progress', 2)
        ->where('stats.completed', 1)
    );
});

test('dashboard shows only 5 most recent tasks', function () {
    $user = User::factory()->create();

    Task::factory()->count(10)->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $response = $this->get(route('dashboard'));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Dashboard')
        ->has('recentTasks', 5)
    );
});

test('dashboard shows zero stats when user has no tasks', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->get(route('dashboard'));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Dashboard')
        ->where('stats.pending', 0)
        ->where('stats.in_progress', 0)
        ->where('stats.completed', 0)
        ->has('recentTasks', 0)
    );
});

test('dashboard only shows tasks belonging to authenticated user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    Task::factory()->count(3)->create(['user_id' => $user->id]);
    Task::factory()->count(5)->create(['user_id' => $otherUser->id]);

    $this->actingAs($user);

    $response = $this->get(route('dashboard'));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->has('recentTasks', 3)
    );
});

test('dashboard recent tasks are ordered by created_at descending', function () {
    $user = User::factory()->create();

    Task::factory()->create([
        'user_id' => $user->id,
        'title' => 'First',
        'created_at' => now()->subDays(3),
    ]);

    Task::factory()->create([
        'user_id' => $user->id,
        'title' => 'Second',
        'created_at' => now()->subDays(2),
    ]);

    Task::factory()->create([
        'user_id' => $user->id,
        'title' => 'Third',
        'created_at' => now()->subDay(),
    ]);

    $this->actingAs($user);

    $response = $this->get(route('dashboard'));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Dashboard')
        ->where('recentTasks.0.title', 'Third')
        ->where('recentTasks.1.title', 'Second')
        ->where('recentTasks.2.title', 'First')
    );
});
