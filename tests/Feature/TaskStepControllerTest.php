<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskStep;
use App\Models\User;

// ─── STORE ──────────────────────────────────────────────────────────────────

test('project member can create a step in a task', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'editor']);
    $task = Task::factory()->create(['project_id' => $project->id]);

    $response = $this->actingAs($user)->postJson(route('steps.store', $task), [
        'name' => 'Paso 1',
    ]);

    $response->assertStatus(201);
    $response->assertJson(['message' => 'Paso creado correctamente.']);
    $response->assertJsonStructure(['step' => ['id', 'name', 'is_completed']]);
    $this->assertDatabaseHas('task_steps', [
        'task_id' => $task->id,
        'name' => 'Paso 1',
        'is_completed' => false,
    ]);
});

test('step position is auto-incremented', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'editor']);
    $task = Task::factory()->create(['project_id' => $project->id]);

    // Crear primer step
    $this->actingAs($user)->postJson(route('steps.store', $task), [
        'name' => 'Paso 1',
    ]);

    // Crear segundo step
    $this->actingAs($user)->postJson(route('steps.store', $task), [
        'name' => 'Paso 2',
    ]);

    $steps = $task->steps()->orderBy('position')->get();
    expect($steps)->toHaveCount(2);
    expect($steps[0]->position)->toBe(0);
    expect($steps[1]->position)->toBe(1);
});

test('step name is required', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'editor']);
    $task = Task::factory()->create(['project_id' => $project->id]);

    $response = $this->actingAs($user)->postJson(route('steps.store', $task), [
        'name' => '',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('name');
});

test('non-member cannot create step', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $task = Task::factory()->create(['project_id' => $project->id]);

    $response = $this->actingAs($user)->postJson(route('steps.store', $task), [
        'name' => 'Intento',
    ]);

    $response->assertStatus(403);
});

// ─── TOGGLE ─────────────────────────────────────────────────────────────────

test('project member can toggle a step', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'editor']);
    $task = Task::factory()->create(['project_id' => $project->id]);
    $step = TaskStep::factory()->create([
        'task_id' => $task->id,
        'is_completed' => false,
    ]);

    $response = $this->actingAs($user)->patchJson(route('steps.toggle', $step));

    $response->assertOk();
    $response->assertJson(['is_completed' => true]);
    expect($step->refresh()->is_completed)->toBeTrue();
});

test('toggling again reverts the step status', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'editor']);
    $task = Task::factory()->create(['project_id' => $project->id]);
    $step = TaskStep::factory()->create([
        'task_id' => $task->id,
        'is_completed' => true,
    ]);

    $response = $this->actingAs($user)->patchJson(route('steps.toggle', $step));

    $response->assertOk();
    $response->assertJson(['is_completed' => false]);
    expect($step->refresh()->is_completed)->toBeFalse();
});

test('non-member cannot toggle step', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $task = Task::factory()->create(['project_id' => $project->id]);
    $step = TaskStep::factory()->create(['task_id' => $task->id]);

    $response = $this->actingAs($user)->patchJson(route('steps.toggle', $step));

    $response->assertStatus(403);
});

// ─── UPDATE ─────────────────────────────────────────────────────────────────

test('project member can update step name', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'editor']);
    $task = Task::factory()->create(['project_id' => $project->id]);
    $step = TaskStep::factory()->create([
        'task_id' => $task->id,
        'name' => 'Nombre original',
    ]);

    $response = $this->actingAs($user)->patchJson(route('steps.update', $step), [
        'name' => 'Nombre actualizado',
    ]);

    $response->assertOk();
    $response->assertJson([
        'step' => ['name' => 'Nombre actualizado'],
    ]);
    expect($step->refresh()->name)->toBe('Nombre actualizado');
});

test('step update name is required', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'editor']);
    $task = Task::factory()->create(['project_id' => $project->id]);
    $step = TaskStep::factory()->create(['task_id' => $task->id]);

    $response = $this->actingAs($user)->patchJson(route('steps.update', $step), [
        'name' => '',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('name');
});

test('non-member cannot update step', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $task = Task::factory()->create(['project_id' => $project->id]);
    $step = TaskStep::factory()->create(['task_id' => $task->id]);

    $response = $this->actingAs($user)->patchJson(route('steps.update', $step), [
        'name' => 'Intento',
    ]);

    $response->assertStatus(403);
});

// ─── DESTROY ────────────────────────────────────────────────────────────────

test('project member can delete a step', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'editor']);
    $task = Task::factory()->create(['project_id' => $project->id]);
    $step = TaskStep::factory()->create(['task_id' => $task->id]);

    $response = $this->actingAs($user)->deleteJson(route('steps.destroy', $step));

    $response->assertOk();
    $response->assertJson(['message' => 'Paso eliminado.']);
    $this->assertDatabaseMissing('task_steps', ['id' => $step->id]);
});

test('non-member cannot delete step', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $task = Task::factory()->create(['project_id' => $project->id]);
    $step = TaskStep::factory()->create(['task_id' => $task->id]);

    $response = $this->actingAs($user)->deleteJson(route('steps.destroy', $step));

    $response->assertStatus(403);
});
