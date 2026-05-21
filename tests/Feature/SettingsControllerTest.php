<?php

use App\Models\User;

test('settings page renders for authenticated user', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('settings.index'));

    $response->assertOk();
});

test('settings page redirects unauthenticated users', function () {
    $response = $this->get(route('settings.index'));

    $response->assertRedirect('/login');
});

test('settings page passes user data to view', function () {
    $user = User::factory()->create([
        'name' => 'Carlos García',
        'email' => 'carlos@example.com',
    ]);

    $response = $this->actingAs($user)->get(route('settings.index'));

    $response->assertOk();
    $response->assertViewHas('userData');

    $userData = $response->viewData('userData');
    expect($userData['name'])->toBe('Carlos García');
    expect($userData['email'])->toBe('carlos@example.com');
    expect($userData['initials'])->toBe('CG');
});

test('settings page with profile tab renders correctly', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('settings.index', ['tab' => 'profile']));

    $response->assertOk();
});

test('profile edit route redirects to settings with profile tab', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('profile.edit'));

    $response->assertRedirect(route('settings.index', ['tab' => 'profile']));
});
