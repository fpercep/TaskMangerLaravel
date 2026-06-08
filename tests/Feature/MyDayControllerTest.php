<?php

use App\Models\Task;
use App\Models\User;
use App\Models\Project;

test('my day page renders for authenticated user', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('mi-dia'));

    $response->assertOk();
    $response->assertViewHasAll(['fechaHoy', 'tareasMasTarde', 'tareasAnteriores']);
});

test('my day redirects unauthenticated users', function () {
    $response = $this->get(route('mi-dia'));

    $response->assertRedirect('/login');
});

test('overdue tasks appear in tareasAnteriores', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'editor']);

    Task::factory()->create([
        'project_id' => $project->id,
        'assigned_user_id' => $user->id,
        'name' => 'Tarea vencida',
        'status' => 'pending',
        'due_date' => now()->subDays(3),
    ]);

    $response = $this->actingAs($user)->get(route('mi-dia'));

    $response->assertOk();
    $tareasAnteriores = $response->viewData('tareasAnteriores');
    expect($tareasAnteriores)->toHaveCount(1);
    expect($tareasAnteriores[0]['name'])->toBe('Tarea vencida');
});

test('future tasks appear in tareasMasTarde', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'editor']);

    Task::factory()->create([
        'project_id' => $project->id,
        'assigned_user_id' => $user->id,
        'name' => 'Tarea futura',
        'status' => 'pending',
        'due_date' => now()->addDays(5),
    ]);

    $response = $this->actingAs($user)->get(route('mi-dia'));

    $response->assertOk();
    $tareasMasTarde = $response->viewData('tareasMasTarde');
    expect($tareasMasTarde)->toHaveCount(1);
    expect($tareasMasTarde[0]['name'])->toBe('Tarea futura');
});

test('completed tasks do not appear in my day', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'editor']);

    Task::factory()->create([
        'project_id' => $project->id,
        'assigned_user_id' => $user->id,
        'name' => 'Tarea completada',
        'status' => 'completed',
        'due_date' => now()->subDay(),
    ]);

    $response = $this->actingAs($user)->get(route('mi-dia'));

    $response->assertOk();
    expect($response->viewData('tareasAnteriores'))->toHaveCount(0);
    expect($response->viewData('tareasMasTarde'))->toHaveCount(0);
});

test('tasks without due date appear in tareasMasTarde', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'editor']);

    Task::factory()->create([
        'project_id' => $project->id,
        'assigned_user_id' => $user->id,
        'name' => 'Tarea sin fecha',
        'status' => 'pending',
        'due_date' => null,
    ]);

    $response = $this->actingAs($user)->get(route('mi-dia'));

    $tareasMasTarde = $response->viewData('tareasMasTarde');
    expect($tareasMasTarde)->toHaveCount(1);
    expect($tareasMasTarde[0]['due_date_fmt'])->toBe('Sin fecha');
});
