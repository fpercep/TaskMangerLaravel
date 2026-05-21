<?php

use App\Models\Task;
use App\Models\TaskStep;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

// ─── SCOPES ─────────────────────────────────────────────────────────────────

test('pending scope filters pending tasks', function () {
    $project = Project::factory()->create();
    Task::factory()->create(['project_id' => $project->id, 'status' => 'pending']);
    Task::factory()->create(['project_id' => $project->id, 'status' => 'completed']);

    expect(Task::pending()->count())->toBe(1);
});

test('inProgress scope filters in_progress tasks', function () {
    $project = Project::factory()->create();
    Task::factory()->create(['project_id' => $project->id, 'status' => 'in_progress']);
    Task::factory()->create(['project_id' => $project->id, 'status' => 'pending']);

    expect(Task::inProgress()->count())->toBe(1);
});

test('completed scope filters completed tasks', function () {
    $project = Project::factory()->create();
    Task::factory()->create(['project_id' => $project->id, 'status' => 'completed']);
    Task::factory()->create(['project_id' => $project->id, 'status' => 'pending']);

    expect(Task::completed()->count())->toBe(1);
});

test('withPriority scope filters by priority', function () {
    $project = Project::factory()->create();
    Task::factory()->create(['project_id' => $project->id, 'priority' => 'urgent']);
    Task::factory()->create(['project_id' => $project->id, 'priority' => 'low']);
    Task::factory()->create(['project_id' => $project->id, 'priority' => 'urgent']);

    expect(Task::withPriority('urgent')->count())->toBe(2);
    expect(Task::withPriority('low')->count())->toBe(1);
});

test('overdue scope filters overdue tasks', function () {
    $project = Project::factory()->create();

    // Overdue: past due_date and not completed/cancelled
    Task::factory()->create([
        'project_id' => $project->id,
        'due_date' => now()->subDays(3),
        'status' => 'pending',
    ]);

    // NOT overdue: past but completed
    Task::factory()->create([
        'project_id' => $project->id,
        'due_date' => now()->subDays(1),
        'status' => 'completed',
    ]);

    // NOT overdue: future date
    Task::factory()->create([
        'project_id' => $project->id,
        'due_date' => now()->addDays(5),
        'status' => 'pending',
    ]);

    // NOT overdue: no due_date
    Task::factory()->create([
        'project_id' => $project->id,
        'due_date' => null,
        'status' => 'pending',
    ]);

    expect(Task::overdue()->count())->toBe(1);
});

test('overdue scope excludes cancelled tasks', function () {
    $project = Project::factory()->create();

    Task::factory()->create([
        'project_id' => $project->id,
        'due_date' => now()->subDays(2),
        'status' => 'cancelled',
    ]);

    expect(Task::overdue()->count())->toBe(0);
});

// ─── RELATIONSHIPS ──────────────────────────────────────────────────────────

test('task belongs to a project', function () {
    $project = Project::factory()->create();
    $task = Task::factory()->create(['project_id' => $project->id]);

    expect($task->project->id)->toBe($project->id);
});

test('task has many steps ordered by position', function () {
    $task = Task::factory()->create();
    TaskStep::factory()->create(['task_id' => $task->id, 'position' => 2, 'name' => 'B']);
    TaskStep::factory()->create(['task_id' => $task->id, 'position' => 0, 'name' => 'A']);
    TaskStep::factory()->create(['task_id' => $task->id, 'position' => 1, 'name' => 'C']);

    $steps = $task->steps;

    expect($steps)->toHaveCount(3);
    expect($steps[0]->position)->toBe(0);
    expect($steps[1]->position)->toBe(1);
    expect($steps[2]->position)->toBe(2);
});

test('task belongs to an assigned user', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['assigned_user_id' => $user->id]);

    expect($task->assignedUser->id)->toBe($user->id);
});

test('task assigned user can be null', function () {
    $task = Task::factory()->create(['assigned_user_id' => null]);

    expect($task->assignedUser)->toBeNull();
});

// ─── CASTS ──────────────────────────────────────────────────────────────────

test('due_date is cast to date', function () {
    $task = Task::factory()->create(['due_date' => '2026-06-15']);

    expect($task->due_date)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
    expect($task->due_date->format('Y-m-d'))->toBe('2026-06-15');
});

test('start_date is cast to date', function () {
    $task = Task::factory()->create(['start_date' => '2026-06-01']);

    expect($task->start_date)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
});

// ─── SOFT DELETE ─────────────────────────────────────────────────────────────

test('task uses soft delete', function () {
    $task = Task::factory()->create();

    $task->delete();

    expect($task->trashed())->toBeTrue();
    expect(Task::withTrashed()->find($task->id))->not->toBeNull();
});
