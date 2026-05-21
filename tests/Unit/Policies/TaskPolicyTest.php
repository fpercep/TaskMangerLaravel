<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

// ─── VIEW ───────────────────────────────────────────────────────────────────

test('project member can view task', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'editor']);
    $task = Task::factory()->create(['project_id' => $project->id]);

    expect($user->can('view', $task))->toBeTrue();
});

test('non-member cannot view task', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $task = Task::factory()->create(['project_id' => $project->id]);

    expect($user->can('view', $task))->toBeFalse();
});

// ─── UPDATE ─────────────────────────────────────────────────────────────────

test('project member can update task', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'editor']);
    $task = Task::factory()->create(['project_id' => $project->id]);

    expect($user->can('update', $task))->toBeTrue();
});

test('non-member cannot update task', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $task = Task::factory()->create(['project_id' => $project->id]);

    expect($user->can('update', $task))->toBeFalse();
});

test('admin can update task', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'admin']);
    $task = Task::factory()->create(['project_id' => $project->id]);

    expect($user->can('update', $task))->toBeTrue();
});

// ─── DELETE ─────────────────────────────────────────────────────────────────

test('project member can delete task', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'editor']);
    $task = Task::factory()->create(['project_id' => $project->id]);

    expect($user->can('delete', $task))->toBeTrue();
});

test('non-member cannot delete task', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $task = Task::factory()->create(['project_id' => $project->id]);

    expect($user->can('delete', $task))->toBeFalse();
});

// ─── CROSS-PROJECT ISOLATION ────────────────────────────────────────────────

test('member of one project cannot access task in another project', function () {
    $user = User::factory()->create();
    $projectA = Project::factory()->create();
    $projectB = Project::factory()->create();
    $projectA->users()->attach($user->id, ['role' => 'admin']);
    // User is NOT a member of projectB
    $task = Task::factory()->create(['project_id' => $projectB->id]);

    expect($user->can('view', $task))->toBeFalse();
    expect($user->can('update', $task))->toBeFalse();
    expect($user->can('delete', $task))->toBeFalse();
});
