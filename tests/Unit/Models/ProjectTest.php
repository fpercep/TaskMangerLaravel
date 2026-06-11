<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);


// ─── RELATIONSHIPS ──────────────────────────────────────────────────────────

test('project has many tasks', function () {
    $project = Project::factory()->create();
    Task::factory()->count(3)->create(['project_id' => $project->id]);

    expect($project->tasks)->toHaveCount(3);
});

test('project has many users', function () {
    $project = Project::factory()->create();
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $project->users()->attach($user1->id, ['role' => 'admin']);
    $project->users()->attach($user2->id, ['role' => 'editor']);

    expect($project->users)->toHaveCount(2);
});

test('editors returns only editors', function () {
    $project = Project::factory()->create();
    $admin = User::factory()->create();
    $editor = User::factory()->create();
    $project->users()->attach($admin->id, ['role' => 'admin']);
    $project->users()->attach($editor->id, ['role' => 'editor']);

    expect($project->editors)->toHaveCount(1);
    expect($project->editors->first()->id)->toBe($editor->id);
});

test('managers returns only managers', function () {
    $project = Project::factory()->create();
    $manager = User::factory()->create();
    $editor = User::factory()->create();
    $project->users()->attach($manager->id, ['role' => 'manager']);
    $project->users()->attach($editor->id, ['role' => 'editor']);

    expect($project->managers)->toHaveCount(1);
    expect($project->managers->first()->id)->toBe($manager->id);
});

test('admins returns only admins', function () {
    $project = Project::factory()->create();
    $admin = User::factory()->create();
    $editor = User::factory()->create();
    $project->users()->attach($admin->id, ['role' => 'admin']);
    $project->users()->attach($editor->id, ['role' => 'editor']);

    expect($project->admins)->toHaveCount(1);
    expect($project->admins->first()->id)->toBe($admin->id);
});

// ─── HELPER METHODS ─────────────────────────────────────────────────────────

test('getMemberIds returns all member ids', function () {
    $project = Project::factory()->create();
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $project->users()->attach($user1->id, ['role' => 'admin']);
    $project->users()->attach($user2->id, ['role' => 'editor']);

    $ids = $project->getMemberIds();

    expect($ids)->toContain($user1->id);
    expect($ids)->toContain($user2->id);
    expect($ids)->toHaveCount(2);
});

test('getOtherMemberIds excludes given user', function () {
    $project = Project::factory()->create();
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $user3 = User::factory()->create();
    $project->users()->attach($user1->id, ['role' => 'admin']);
    $project->users()->attach($user2->id, ['role' => 'editor']);
    $project->users()->attach($user3->id, ['role' => 'editor']);

    $ids = $project->getOtherMemberIds($user1->id);

    expect($ids)->toContain($user2->id);
    expect($ids)->toContain($user3->id);
    expect($ids)->not->toContain($user1->id);
    expect($ids)->toHaveCount(2);
});

test('getOtherMemberIds with null returns all members', function () {
    $project = Project::factory()->create();
    $user1 = User::factory()->create();
    $project->users()->attach($user1->id, ['role' => 'admin']);

    $ids = $project->getOtherMemberIds(null);

    expect($ids)->toContain($user1->id);
});

// ─── SOFT DELETE ─────────────────────────────────────────────────────────────

test('project uses soft delete', function () {
    $project = Project::factory()->create();

    $project->delete();

    expect($project->trashed())->toBeTrue();
    expect(Project::withTrashed()->find($project->id))->not->toBeNull();
});
