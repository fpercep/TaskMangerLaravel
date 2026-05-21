<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskStep;
use App\Models\User;

// ─── STORE ──────────────────────────────────────────────────────────────────

test('project member can create a task', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'editor']);

    $response = $this->actingAs($user)->post(route('tasks.store', $project), [
        'name' => 'Nueva tarea',
        'status' => 'pending',
        'priority' => 'medium',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Tarea creada correctamente.');
    $this->assertDatabaseHas('tasks', [
        'project_id' => $project->id,
        'name' => 'Nueva tarea',
        'status' => 'pending',
        'priority' => 'medium',
    ]);
});

test('non-member cannot create a task in a project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    $response = $this->actingAs($user)->post(route('tasks.store', $project), [
        'name' => 'Tarea intrusa',
        'status' => 'pending',
        'priority' => 'low',
    ]);

    $response->assertStatus(403);
});

test('task name is required', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'editor']);

    $response = $this->actingAs($user)->post(route('tasks.store', $project), [
        'name' => '',
        'status' => 'pending',
        'priority' => 'low',
    ]);

    $response->assertSessionHasErrors('name');
});

test('task status must be valid', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'editor']);

    $response = $this->actingAs($user)->post(route('tasks.store', $project), [
        'name' => 'Tarea',
        'status' => 'invalid_status',
        'priority' => 'low',
    ]);

    $response->assertSessionHasErrors('status');
});

test('task priority must be valid', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'editor']);

    $response = $this->actingAs($user)->post(route('tasks.store', $project), [
        'name' => 'Tarea',
        'status' => 'pending',
        'priority' => 'super_high',
    ]);

    $response->assertSessionHasErrors('priority');
});

test('task can be created with optional fields', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'editor']);

    $response = $this->actingAs($user)->post(route('tasks.store', $project), [
        'name' => 'Tarea completa',
        'description' => 'Descripción detallada',
        'status' => 'in_progress',
        'priority' => 'high',
        'due_date' => '2026-12-31',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('tasks', [
        'name' => 'Tarea completa',
        'description' => 'Descripción detallada',
        'priority' => 'high',
    ]);
});

// ─── UPDATE ─────────────────────────────────────────────────────────────────

test('project member can update a task', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'editor']);
    $task = Task::factory()->create(['project_id' => $project->id]);

    $response = $this->actingAs($user)->patchJson(route('tasks.update', $task), [
        'name' => 'Tarea actualizada',
    ]);

    $response->assertOk();
    $response->assertJson(['message' => 'Tarea actualizada correctamente']);
    expect($task->refresh()->name)->toBe('Tarea actualizada');
});

test('non-member cannot update a task', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $task = Task::factory()->create(['project_id' => $project->id]);

    $response = $this->actingAs($user)->patchJson(route('tasks.update', $task), [
        'name' => 'Intento',
    ]);

    $response->assertStatus(403);
});

test('task can be partially updated', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'editor']);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'name' => 'Original',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($user)->patchJson(route('tasks.update', $task), [
        'status' => 'completed',
    ]);

    $response->assertOk();
    $task->refresh();
    expect($task->status)->toBe('completed');
    expect($task->name)->toBe('Original');
});

test('task can be assigned to a project member', function () {
    $admin = User::factory()->create();
    $editor = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($admin->id, ['role' => 'admin']);
    $project->users()->attach($editor->id, ['role' => 'editor']);
    $task = Task::factory()->create(['project_id' => $project->id]);

    $response = $this->actingAs($admin)->patchJson(route('tasks.update', $task), [
        'assigned_user_id' => $editor->id,
    ]);

    $response->assertOk();
    expect($task->refresh()->assigned_user_id)->toBe($editor->id);
});

test('task cannot be assigned to a non-member', function () {
    $admin = User::factory()->create();
    $outsider = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($admin->id, ['role' => 'admin']);
    $task = Task::factory()->create(['project_id' => $project->id]);

    $response = $this->actingAs($admin)->patchJson(route('tasks.update', $task), [
        'assigned_user_id' => $outsider->id,
    ]);

    $response->assertStatus(422);
});

// ─── DUPLICATE ──────────────────────────────────────────────────────────────

test('project member can duplicate a task', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'editor']);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'assigned_user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->postJson(route('tasks.duplicate', $task));

    $response->assertOk();
    $response->assertJson(['message' => 'Tarea duplicada correctamente']);

    // La tarea original sigue existiendo
    expect(Task::where('project_id', $project->id)->count())->toBe(2);
});

test('duplicated task has no assigned user', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'editor']);
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'assigned_user_id' => $user->id,
    ]);

    $this->actingAs($user)->postJson(route('tasks.duplicate', $task));

    $duplicated = Task::where('project_id', $project->id)
        ->where('id', '!=', $task->id)
        ->first();

    expect($duplicated)->not->toBeNull();
    expect($duplicated->assigned_user_id)->toBeNull();
});

test('duplicated task copies steps', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'editor']);
    $task = Task::factory()->create(['project_id' => $project->id]);

    TaskStep::factory()->count(3)->create(['task_id' => $task->id]);

    $this->actingAs($user)->postJson(route('tasks.duplicate', $task));

    $duplicated = Task::where('project_id', $project->id)
        ->where('id', '!=', $task->id)
        ->first();

    expect($duplicated->steps()->count())->toBe(3);
});

test('non-member cannot duplicate a task', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $task = Task::factory()->create(['project_id' => $project->id]);

    $response = $this->actingAs($user)->postJson(route('tasks.duplicate', $task));

    $response->assertStatus(403);
});

// ─── DESTROY ────────────────────────────────────────────────────────────────

test('project member can delete a task', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'editor']);
    $task = Task::factory()->create(['project_id' => $project->id]);

    $response = $this->actingAs($user)->deleteJson(route('tasks.destroy', $task));

    $response->assertOk();
    $response->assertJson(['message' => 'Tarea eliminada correctamente']);
    expect($task->fresh()->trashed())->toBeTrue();
});

test('non-member cannot delete a task', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $task = Task::factory()->create(['project_id' => $project->id]);

    $response = $this->actingAs($user)->deleteJson(route('tasks.destroy', $task));

    $response->assertStatus(403);
});

test('deleted task uses soft delete', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'editor']);
    $task = Task::factory()->create(['project_id' => $project->id]);

    $this->actingAs($user)->deleteJson(route('tasks.destroy', $task));

    $this->assertSoftDeleted('tasks', ['id' => $task->id]);
});
