<?php

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

// ─── INITIALS ATTRIBUTE ─────────────────────────────────────────────────────

test('initials returns first letters of first and last name', function () {
    $user = User::factory()->create(['name' => 'Carlos García']);

    expect($user->initials)->toBe('CG');
});

test('initials returns single letter for single name', function () {
    $user = User::factory()->create(['name' => 'Ana']);

    expect($user->initials)->toBe('A');
});

test('initials uses only first two words for long names', function () {
    $user = User::factory()->create(['name' => 'Juan Carlos López Martínez']);

    expect($user->initials)->toBe('JC');
});

test('initials handles unicode characters', function () {
    $user = User::factory()->create(['name' => 'Ángela Ñoño']);

    expect($user->initials)->toBe('ÁÑ');
});

// ─── SEARCH SCOPE ───────────────────────────────────────────────────────────

test('search scope filters by name', function () {
    User::factory()->create(['name' => 'Carlos García', 'email' => 'carlos@test.com']);
    User::factory()->create(['name' => 'Ana López', 'email' => 'ana@test.com']);

    $results = User::search('Carlos')->get();

    expect($results)->toHaveCount(1);
    expect($results->first()->name)->toBe('Carlos García');
});

test('search scope filters by email', function () {
    User::factory()->create(['name' => 'Carlos', 'email' => 'carlos@special.com']);
    User::factory()->create(['name' => 'Ana', 'email' => 'ana@test.com']);

    $results = User::search('special.com')->get();

    expect($results)->toHaveCount(1);
    expect($results->first()->email)->toBe('carlos@special.com');
});

test('search scope returns all when term is null', function () {
    User::factory()->count(3)->create();

    $results = User::search(null)->get();

    expect($results)->toHaveCount(3);
});

test('search scope returns all when term is empty string', function () {
    User::factory()->count(2)->create();

    $results = User::search('')->get();

    expect($results)->toHaveCount(2);
});

// ─── RELATIONSHIPS ──────────────────────────────────────────────────────────

test('user has many tasks', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    Task::factory()->count(3)->create([
        'project_id' => $project->id,
        'assigned_user_id' => $user->id,
    ]);

    expect($user->tasks)->toHaveCount(3);
});

test('user belongs to many projects', function () {
    $user = User::factory()->create();
    $project1 = Project::factory()->create();
    $project2 = Project::factory()->create();
    $project1->users()->attach($user->id, ['role' => 'admin']);
    $project2->users()->attach($user->id, ['role' => 'editor']);

    expect($user->projects)->toHaveCount(2);
});

test('user project pivot includes role', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'manager']);

    $pivot = $user->projects->first()->pivot;

    expect($pivot->role)->toBe('manager');
});

// ─── CASTS ──────────────────────────────────────────────────────────────────

test('is_super_admin is cast to boolean', function () {
    $user = User::factory()->create(['is_super_admin' => 1]);

    expect($user->is_super_admin)->toBeTrue();
    expect($user->is_super_admin)->toBeBool();
});

test('password is hidden in serialization', function () {
    $user = User::factory()->create();
    $array = $user->toArray();

    expect($array)->not->toHaveKey('password');
    expect($array)->not->toHaveKey('remember_token');
});
