<?php

use App\Models\Project;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertSoftDeleted;

// ==============================================
// AUTHENTICATION TESTS
// ==============================================

test('guest cannot access projects index', function () {
    $response = $this->get(route('projects.index'));

    $response->assertRedirect(route('login'));
});

test('guest cannot create project', function () {
    $response = $this->post(route('projects.store'), [
        'name' => 'Test Project',
        'description' => 'Test Description',
    ]);

    $response->assertRedirect(route('login'));
});

test('guest cannot view project', function () {
    $project = Project::factory()->create();

    $response = $this->get(route('projects.show', $project));

    $response->assertRedirect(route('login'));
});

test('guest cannot update project', function () {
    $project = Project::factory()->create();

    $response = $this->patch(route('projects.update', $project), [
        'name' => 'Updated Name',
    ]);

    $response->assertRedirect(route('login'));
});

test('guest cannot delete project', function () {
    $project = Project::factory()->create();

    $response = $this->delete(route('projects.destroy', $project));

    $response->assertRedirect(route('login'));
});

// ==============================================
// INDEX TESTS
// ==============================================

test('authenticated user can see their projects list', function () {
    $user = User::factory()->create();
    Project::factory()->count(3)->forUser($user)->create();

    actingAs($user);

    $response = $this->get(route('projects.index'));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Projects/Index')
        ->has('projects.data', 3)
    );
});

test('user only sees their own projects', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    Project::factory()->forUser($user)->create(['name' => 'My Project']);
    Project::factory()->forUser($otherUser)->create(['name' => 'Other Project']);

    actingAs($user);

    $response = $this->get(route('projects.index'));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Projects/Index')
        ->has('projects.data', 1)
        ->where('projects.data.0.name', 'My Project')
    );
});

test('projects are ordered correctly', function () {
    $user = User::factory()->create();

    Project::factory()->forUser($user)->create(['name' => 'First', 'created_at' => now()->subDays(2)]);
    Project::factory()->forUser($user)->create(['name' => 'Second', 'created_at' => now()->subDay()]);
    Project::factory()->forUser($user)->create(['name' => 'Third', 'created_at' => now()]);

    actingAs($user);

    $response = $this->get(route('projects.index'));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Projects/Index')
        ->where('projects.data.0.name', 'Third')
        ->where('projects.data.1.name', 'Second')
        ->where('projects.data.2.name', 'First')
    );
});

// ==============================================
// STORE TESTS
// ==============================================

test('authenticated user can create project with valid data', function () {
    $user = User::factory()->create();

    actingAs($user);

    $projectData = [
        'name' => 'New Project',
        'description' => 'Project Description',
        'color' => '#FF5733',
    ];

    $response = $this->post(route('projects.store'), $projectData);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Projeto criado com sucesso!');

    assertDatabaseHas('projects', [
        'name' => 'New Project',
        'description' => 'Project Description',
        'color' => '#FF5733',
        'user_id' => $user->id,
    ]);
});

test('project is associated with authenticated user', function () {
    $user = User::factory()->create();

    actingAs($user);

    $this->post(route('projects.store'), [
        'name' => 'User Project',
    ]);

    $project = Project::where('name', 'User Project')->first();

    expect($project->user_id)->toBe($user->id);
});

test('project name is required', function () {
    $user = User::factory()->create();

    actingAs($user);

    $response = $this->post(route('projects.store'), [
        'name' => '',
        'description' => 'Some description',
    ]);

    $response->assertSessionHasErrors('name');
});

test('project description is optional', function () {
    $user = User::factory()->create();

    actingAs($user);

    $response = $this->post(route('projects.store'), [
        'name' => 'Project Without Description',
    ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();

    assertDatabaseHas('projects', [
        'name' => 'Project Without Description',
        'description' => null,
        'user_id' => $user->id,
    ]);
});

// ==============================================
// SHOW TESTS
// ==============================================

test('user can view their own project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create();

    actingAs($user);

    $response = $this->get(route('projects.show', $project));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Projects/Show')
        ->where('project.data.id', $project->id)
        ->where('project.data.name', $project->name)
    );
});

test('user cannot view another user project', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $otherProject = Project::factory()->forUser($otherUser)->create();

    actingAs($user);

    $response = $this->get(route('projects.show', $otherProject));

    $response->assertForbidden();
});

// ==============================================
// UPDATE TESTS
// ==============================================

test('user can update their own project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create([
        'name' => 'Original Name',
        'description' => 'Original Description',
    ]);

    actingAs($user);

    $response = $this->patch(route('projects.update', $project), [
        'name' => 'Updated Name',
        'description' => 'Updated Description',
        'color' => '#00FF00',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Projeto atualizado com sucesso!');

    assertDatabaseHas('projects', [
        'id' => $project->id,
        'name' => 'Updated Name',
        'description' => 'Updated Description',
        'color' => '#00FF00',
    ]);
});

test('user cannot update another user project', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $otherProject = Project::factory()->forUser($otherUser)->create(['name' => 'Other Project']);

    actingAs($user);

    $response = $this->patch(route('projects.update', $otherProject), [
        'name' => 'Hacked Name',
    ]);

    $response->assertForbidden();

    assertDatabaseHas('projects', [
        'id' => $otherProject->id,
        'name' => 'Other Project',
    ]);
});

test('project data is updated correctly in database', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create([
        'name' => 'Old Name',
        'description' => 'Old Description',
        'color' => '#000000',
    ]);

    actingAs($user);

    $this->patch(route('projects.update', $project), [
        'name' => 'New Name',
        'description' => 'New Description',
        'color' => '#FFFFFF',
    ]);

    $project->refresh();

    expect($project->name)->toBe('New Name');
    expect($project->description)->toBe('New Description');
    expect($project->color)->toBe('#FFFFFF');
});

// ==============================================
// DESTROY TESTS
// ==============================================

test('user can delete their own project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create();

    actingAs($user);

    $response = $this->delete(route('projects.destroy', $project));

    $response->assertRedirect(route('projects.index'));
    $response->assertSessionHas('success', 'Projeto deletado com sucesso!');

    assertSoftDeleted('projects', [
        'id' => $project->id,
    ]);
});

test('user cannot delete another user project', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $otherProject = Project::factory()->forUser($otherUser)->create();

    actingAs($user);

    $response = $this->delete(route('projects.destroy', $otherProject));

    $response->assertForbidden();

    assertDatabaseHas('projects', [
        'id' => $otherProject->id,
        'deleted_at' => null,
    ]);
});

test('project is soft deleted not permanently removed', function () {
    $user = User::factory()->create();
    $project = Project::factory()->forUser($user)->create();

    actingAs($user);

    $this->delete(route('projects.destroy', $project));

    assertSoftDeleted('projects', [
        'id' => $project->id,
    ]);

    $deletedProject = Project::withTrashed()->find($project->id);
    expect($deletedProject)->not->toBeNull();
    expect($deletedProject->trashed())->toBeTrue();
});
