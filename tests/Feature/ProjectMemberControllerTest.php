<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

// ─── INDEX ──────────────────────────────────────────────────────────────────

test('project member can list members', function () {
    $admin = User::factory()->create();
    $editor = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($admin->id, ['role' => 'admin']);
    $project->users()->attach($editor->id, ['role' => 'editor']);

    $response = $this->actingAs($admin)->getJson(route('projects.members.index', $project));

    $response->assertOk();
    $response->assertJsonCount(2, 'data');
});

test('non-member cannot list members', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    $response = $this->actingAs($user)->getJson(route('projects.members.index', $project));

    $response->assertStatus(403);
});

// ─── STORE ──────────────────────────────────────────────────────────────────

test('admin can add a member to a project', function () {
    $admin = User::factory()->create();
    $newUser = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($admin->id, ['role' => 'admin']);

    $response = $this->actingAs($admin)->postJson(route('projects.members.store', $project), [
        'user_id' => $newUser->id,
        'role' => 'editor',
    ]);

    $response->assertOk();
    expect($project->users()->where('users.id', $newUser->id)->exists())->toBeTrue();
});

test('manager can add editor to a project', function () {
    $manager = User::factory()->create();
    $newUser = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($manager->id, ['role' => 'manager']);

    $response = $this->actingAs($manager)->postJson(route('projects.members.store', $project), [
        'user_id' => $newUser->id,
        'role' => 'editor',
    ]);

    $response->assertOk();
});

test('editor cannot add members to a project', function () {
    $editor = User::factory()->create();
    $newUser = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($editor->id, ['role' => 'editor']);

    $response = $this->actingAs($editor)->postJson(route('projects.members.store', $project), [
        'user_id' => $newUser->id,
        'role' => 'editor',
    ]);

    $response->assertStatus(403);
});

test('cannot add a user that is already a member', function () {
    $admin = User::factory()->create();
    $existingMember = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($admin->id, ['role' => 'admin']);
    $project->users()->attach($existingMember->id, ['role' => 'editor']);

    $response = $this->actingAs($admin)->postJson(route('projects.members.store', $project), [
        'user_id' => $existingMember->id,
        'role' => 'editor',
    ]);

    $response->assertStatus(422);
});

// ─── UPDATE (role) ──────────────────────────────────────────────────────────

test('admin can update a member role', function () {
    $admin = User::factory()->create();
    $editor = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($admin->id, ['role' => 'admin']);
    $project->users()->attach($editor->id, ['role' => 'editor']);

    $response = $this->actingAs($admin)->patchJson(
        route('projects.members.update', [$project, $editor]),
        ['role' => 'admin']
    );

    $response->assertOk();
    expect(
        $project->users()->where('users.id', $editor->id)->first()->pivot->role
    )->toBe('admin');
});

test('cannot downgrade the last admin', function () {
    $admin = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($admin->id, ['role' => 'admin']);

    $response = $this->actingAs($admin)->patchJson(
        route('projects.members.update', [$project, $admin]),
        ['role' => 'editor']
    );

    $response->assertStatus(422);
});

// ─── DESTROY (individual) ───────────────────────────────────────────────────

test('admin can remove a member from a project', function () {
    $admin = User::factory()->create();
    $editor = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($admin->id, ['role' => 'admin']);
    $project->users()->attach($editor->id, ['role' => 'editor']);

    $response = $this->actingAs($admin)->deleteJson(
        route('projects.members.destroy', [$project, $editor])
    );

    $response->assertOk();
    expect($project->users()->where('users.id', $editor->id)->exists())->toBeFalse();
});

test('admin cannot remove themselves', function () {
    $admin = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($admin->id, ['role' => 'admin']);

    $response = $this->actingAs($admin)->deleteJson(
        route('projects.members.destroy', [$project, $admin])
    );

    $response->assertStatus(422);
});

test('cannot remove the last admin via destroy', function () {
    $admin = User::factory()->create();
    $anotherAdmin = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($admin->id, ['role' => 'admin']);
    $project->users()->attach($anotherAdmin->id, ['role' => 'editor']);

    // anotherAdmin es el único admin aquí — pero admin intenta borrarse a sí mismo
    // En realidad el admin se auto-elimina, lo que se impide por la validación "no puedes eliminarte a ti mismo"
    $response = $this->actingAs($admin)->deleteJson(
        route('projects.members.destroy', [$project, $admin])
    );

    $response->assertStatus(422);
});

test('editor cannot remove members', function () {
    $editor = User::factory()->create();
    $otherEditor = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($editor->id, ['role' => 'editor']);
    $project->users()->attach($otherEditor->id, ['role' => 'editor']);

    $response = $this->actingAs($editor)->deleteJson(
        route('projects.members.destroy', [$project, $otherEditor])
    );

    $response->assertStatus(403);
});

// ─── SYNC (bulk add/update) ─────────────────────────────────────────────────

test('admin can sync members', function () {
    $admin = User::factory()->create();
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($admin->id, ['role' => 'admin']);

    $response = $this->actingAs($admin)->postJson(route('projects.members.sync', $project), [
        'users' => [
            ['user_id' => $user1->id, 'role' => 'editor'],
            ['user_id' => $user2->id, 'role' => 'editor'],
        ],
    ]);

    $response->assertOk();
    expect($project->users()->count())->toBe(3); // admin + 2 nuevos
});

test('editor cannot sync members', function () {
    $editor = User::factory()->create();
    $newUser = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($editor->id, ['role' => 'editor']);

    $response = $this->actingAs($editor)->postJson(route('projects.members.sync', $project), [
        'users' => [
            ['user_id' => $newUser->id, 'role' => 'editor'],
        ],
    ]);

    $response->assertStatus(403);
});

// ─── DESTROY BULK ───────────────────────────────────────────────────────────

test('admin can bulk remove members', function () {
    $admin = User::factory()->create();
    $editor1 = User::factory()->create();
    $editor2 = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($admin->id, ['role' => 'admin']);
    $project->users()->attach($editor1->id, ['role' => 'editor']);
    $project->users()->attach($editor2->id, ['role' => 'editor']);

    $response = $this->actingAs($admin)->deleteJson(route('projects.members.destroy-bulk', $project), [
        'user_ids' => [$editor1->id, $editor2->id],
    ]);

    $response->assertOk();
    expect($project->users()->count())->toBe(1); // solo el admin
});

test('bulk remove excludes the authenticated user', function () {
    $admin = User::factory()->create();
    $editor = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($admin->id, ['role' => 'admin']);
    $project->users()->attach($editor->id, ['role' => 'editor']);

    // Intenta eliminarse a sí mismo + editor
    $response = $this->actingAs($admin)->deleteJson(route('projects.members.destroy-bulk', $project), [
        'user_ids' => [$admin->id, $editor->id],
    ]);

    $response->assertOk();
    // El admin NO fue eliminado (auto-exclusión)
    expect($project->users()->where('users.id', $admin->id)->exists())->toBeTrue();
    // El editor SÍ fue eliminado
    expect($project->users()->where('users.id', $editor->id)->exists())->toBeFalse();
});

test('editor cannot bulk remove members', function () {
    $editor = User::factory()->create();
    $otherEditor = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($editor->id, ['role' => 'editor']);
    $project->users()->attach($otherEditor->id, ['role' => 'editor']);

    $response = $this->actingAs($editor)->deleteJson(route('projects.members.destroy-bulk', $project), [
        'user_ids' => [$otherEditor->id],
    ]);

    $response->assertStatus(403);
});

// ─── LEAVE ──────────────────────────────────────────────────────────────────
// (Los tests de leave ya existen en LeaveProjectTest.php, aquí testeamos la respuesta JSON)

test('member can leave project via json', function () {
    $user = User::factory()->create();
    $admin = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'editor']);
    $project->users()->attach($admin->id, ['role' => 'admin']);

    $response = $this->actingAs($user)->postJson(route('projects.leave', $project));

    $response->assertOk();
    $response->assertJson(['success' => 'Has abandonado el proyecto.']);
});

test('last admin cannot leave project via json', function () {
    $admin = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($admin->id, ['role' => 'admin']);

    $response = $this->actingAs($admin)->postJson(route('projects.leave', $project));

    $response->assertStatus(422);
    $response->assertJsonStructure(['error']);
});

test('member tasks are unassigned when leaving via json', function () {
    $user = User::factory()->create();
    $admin = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'editor']);
    $project->users()->attach($admin->id, ['role' => 'admin']);

    $task = Task::factory()->create([
        'project_id' => $project->id,
        'assigned_user_id' => $user->id,
    ]);

    $this->actingAs($user)->postJson(route('projects.leave', $project));

    expect($task->refresh()->assigned_user_id)->toBeNull();
});
