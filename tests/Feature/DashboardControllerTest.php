<?php

use App\Models\Task;
use App\Models\User;
use App\Models\Project;

test('dashboard renders for authenticated user', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
});

test('dashboard redirects unauthenticated users', function () {
    $response = $this->get(route('dashboard'));

    $response->assertRedirect('/login');
});

test('dashboard shows task statistics', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'editor']);

    Task::factory()->create([
        'project_id' => $project->id,
        'assigned_user_id' => $user->id,
        'status' => 'pending',
    ]);
    Task::factory()->create([
        'project_id' => $project->id,
        'assigned_user_id' => $user->id,
        'status' => 'in_progress',
    ]);
    Task::factory()->count(2)->create([
        'project_id' => $project->id,
        'assigned_user_id' => $user->id,
        'status' => 'completed',
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertViewHas('estadisticas');

    $estadisticas = $response->viewData('estadisticas');
    // Pendientes
    expect($estadisticas[0]['valor'])->toBe(1);
    // En Progreso
    expect($estadisticas[1]['valor'])->toBe(1);
    // Completadas
    expect($estadisticas[2]['valor'])->toBe(2);
});

test('dashboard shows zero stats when user has no tasks', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $estadisticas = $response->viewData('estadisticas');

    expect($estadisticas[0]['valor'])->toBe(0);
    expect($estadisticas[1]['valor'])->toBe(0);
    expect($estadisticas[2]['valor'])->toBe(0);
});
