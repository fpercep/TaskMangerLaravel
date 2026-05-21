<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

// ─── STORE ──────────────────────────────────────────────────────────────────

test('authenticated user can create a project', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('projects.store'), [
        'name' => 'Mi Proyecto',
        'description' => 'Descripción del proyecto',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Proyecto creado correctamente.');

    $this->assertDatabaseHas('projects', ['name' => 'Mi Proyecto']);
});

test('creator is assigned as admin of the new project', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('projects.store'), [
        'name' => 'Mi Proyecto Admin',
    ]);

    $project = Project::where('name', 'Mi Proyecto Admin')->first();

    expect($project)->not->toBeNull();
    expect($project->users()->where('users.id', $user->id)->first()->pivot->role)->toBe('admin');
});

test('project name is required', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('projects.store'), [
        'name' => '',
    ]);

    $response->assertSessionHasErrors('name');
});

test('project name cannot exceed 255 characters', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('projects.store'), [
        'name' => str_repeat('A', 256),
    ]);

    $response->assertSessionHasErrors('name');
});

test('project description is optional', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('projects.store'), [
        'name' => 'Proyecto sin descripción',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
});

test('unauthenticated user cannot create project', function () {
    $response = $this->post(route('projects.store'), [
        'name' => 'Proyecto',
    ]);

    $response->assertRedirect('/login');
});

// ─── SHOW ───────────────────────────────────────────────────────────────────

test('project member can view a project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'editor']);

    $response = $this->actingAs($user)->get(route('projects.show', $project));

    $response->assertOk();
});

test('non-member cannot view a private project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['visibility' => 'private']);

    $response = $this->actingAs($user)->get(route('projects.show', $project));

    $response->assertStatus(403);
});

test('unauthenticated user cannot view project', function () {
    $project = Project::factory()->create();

    $response = $this->get(route('projects.show', $project));

    $response->assertRedirect('/login');
});

// ─── UPDATE ─────────────────────────────────────────────────────────────────

test('admin can update a project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'admin']);

    $response = $this->actingAs($user)->put(route('projects.update', $project), [
        'name' => 'Nombre Actualizado',
        'description' => 'Descripción actualizada',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Proyecto actualizado correctamente.');
    expect($project->refresh()->name)->toBe('Nombre Actualizado');
});

test('manager can update a project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'manager']);

    $response = $this->actingAs($user)->put(route('projects.update', $project), [
        'name' => 'Nombre Manager',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
});

test('editor cannot update a project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'editor']);

    $response = $this->actingAs($user)->put(route('projects.update', $project), [
        'name' => 'Intento de editor',
    ]);

    $response->assertStatus(403);
});

test('non-member cannot update a project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    $response = $this->actingAs($user)->put(route('projects.update', $project), [
        'name' => 'Intento de outsider',
    ]);

    $response->assertStatus(403);
});

// ─── DESTROY ────────────────────────────────────────────────────────────────

test('admin can delete a project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'admin']);

    $response = $this->actingAs($user)->delete(route('projects.destroy', $project));

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Proyecto eliminado correctamente.');
    expect($project->fresh()->trashed())->toBeTrue();
});

test('non-admin cannot delete a project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'manager']);

    $response = $this->actingAs($user)->delete(route('projects.destroy', $project));

    $response->assertStatus(403);
});

test('editor cannot delete a project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'editor']);

    $response = $this->actingAs($user)->delete(route('projects.destroy', $project));

    $response->assertStatus(403);
});

test('project uses soft delete', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'admin']);

    $this->actingAs($user)->delete(route('projects.destroy', $project));

    $this->assertSoftDeleted('projects', ['id' => $project->id]);
});
