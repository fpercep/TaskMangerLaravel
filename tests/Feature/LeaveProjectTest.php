<?php

use App\Models\Project;
use App\Models\User;
use App\Models\Task;

test('a member with editor role can leave a project and their tasks are unassigned', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    
    // Attach user as editor
    $project->users()->attach($user->id, ['role' => 'editor']);
    
    // Add another user as admin so the project is not left without admins
    $admin = User::factory()->create();
    $project->users()->attach($admin->id, ['role' => 'admin']);

    // Create a task assigned to the leaving user
    $task = Task::factory()->create([
        'project_id' => $project->id,
        'assigned_user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->post(route('projects.leave', $project));

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('success', 'Has abandonado el proyecto.');

    // Assert user is no longer a member
    expect($project->users()->where('users.id', $user->id)->exists())->toBeFalse();
    
    // Assert task is unassigned
    expect($task->refresh()->assigned_user_id)->toBeNull();
});

test('a member with manager role can leave a project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    
    $project->users()->attach($user->id, ['role' => 'manager']);
    
    $admin = User::factory()->create();
    $project->users()->attach($admin->id, ['role' => 'admin']);

    $response = $this->actingAs($user)->post(route('projects.leave', $project));

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('success', 'Has abandonado el proyecto.');

    expect($project->users()->where('users.id', $user->id)->exists())->toBeFalse();
});

test('an admin can leave a project if there is another admin', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    
    $project->users()->attach($user->id, ['role' => 'admin']);
    
    $otherAdmin = User::factory()->create();
    $project->users()->attach($otherAdmin->id, ['role' => 'admin']);

    $response = $this->actingAs($user)->post(route('projects.leave', $project));

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('success', 'Has abandonado el proyecto.');

    expect($project->users()->where('users.id', $user->id)->exists())->toBeFalse();
});

test('an admin cannot leave a project if they are the last admin', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    
    $project->users()->attach($user->id, ['role' => 'admin']);
    
    // Add an editor, but no other admin
    $editor = User::factory()->create();
    $project->users()->attach($editor->id, ['role' => 'editor']);

    $response = $this->actingAs($user)->post(route('projects.leave', $project));

    $response->assertRedirect();
    $response->assertSessionHas('error');

    // Assert user is still a member and admin
    expect($project->users()->where('users.id', $user->id)->wherePivot('role', 'admin')->exists())->toBeTrue();
});

test('a non-member cannot leave a project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    
    // Add some admin to project
    $admin = User::factory()->create();
    $project->users()->attach($admin->id, ['role' => 'admin']);

    $response = $this->actingAs($user)->post(route('projects.leave', $project));

    $response->assertStatus(403);
});
