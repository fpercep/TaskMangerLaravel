<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

// ─── VIEW ───────────────────────────────────────────────────────────────────

test('member can view project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'editor']);

    expect($user->can('view', $project))->toBeTrue();
});

test('non-member cannot view project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    expect($user->can('view', $project))->toBeFalse();
});

// ─── CREATE ─────────────────────────────────────────────────────────────────

test('any authenticated user can create projects', function () {
    $user = User::factory()->create();

    expect($user->can('create', Project::class))->toBeTrue();
});

// ─── UPDATE ─────────────────────────────────────────────────────────────────

test('admin can update project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'admin']);

    expect($user->can('update', $project))->toBeTrue();
});

test('manager can update project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'manager']);

    expect($user->can('update', $project))->toBeTrue();
});

test('editor cannot update project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'editor']);

    expect($user->can('update', $project))->toBeFalse();
});

test('non-member cannot update project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    expect($user->can('update', $project))->toBeFalse();
});

// ─── DELETE ─────────────────────────────────────────────────────────────────

test('admin can delete project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'admin']);

    expect($user->can('delete', $project))->toBeTrue();
});

test('manager cannot delete project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'manager']);

    expect($user->can('delete', $project))->toBeFalse();
});

test('editor cannot delete project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'editor']);

    expect($user->can('delete', $project))->toBeFalse();
});

// ─── MANAGE MEMBERS ─────────────────────────────────────────────────────────

test('admin can manage members', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'admin']);

    expect($user->can('manageMembers', $project))->toBeTrue();
});

test('manager can manage members', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'manager']);

    expect($user->can('manageMembers', $project))->toBeTrue();
});

test('editor cannot manage members', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'editor']);

    expect($user->can('manageMembers', $project))->toBeFalse();
});

// ─── LEAVE ──────────────────────────────────────────────────────────────────

test('member can leave project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'editor']);

    expect($user->can('leave', $project))->toBeTrue();
});

test('non-member cannot leave project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    expect($user->can('leave', $project))->toBeFalse();
});

// ─── RESTORE / FORCE DELETE ─────────────────────────────────────────────────

test('nobody can restore a project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'admin']);

    expect($user->can('restore', $project))->toBeFalse();
});

test('nobody can force delete a project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'admin']);

    expect($user->can('forceDelete', $project))->toBeFalse();
});
