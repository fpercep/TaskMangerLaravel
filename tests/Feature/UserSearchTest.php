<?php

use App\Models\User;

test('authenticated user can search users by name', function () {
    User::factory()->create(['name' => 'Ana Martínez', 'email' => 'ana@example.com']);
    User::factory()->create(['name' => 'Pedro García', 'email' => 'pedro@example.com']);
    $searcher = User::factory()->create();

    $response = $this->actingAs($searcher)->getJson(route('users.search', ['search' => 'Ana']));

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.name', 'Ana Martínez');
});

test('authenticated user can search users by email', function () {
    User::factory()->create(['name' => 'Ana Martínez', 'email' => 'ana@example.com']);
    $searcher = User::factory()->create();

    $response = $this->actingAs($searcher)->getJson(route('users.search', ['search' => 'ana@example']));

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
});

test('search results are limited to 5', function () {
    User::factory()->count(10)->create(['name' => 'TestUser']);
    $searcher = User::factory()->create();

    $response = $this->actingAs($searcher)->getJson(route('users.search', ['search' => 'TestUser']));

    $response->assertOk();
    $response->assertJsonCount(5, 'data');
});

test('empty search returns users', function () {
    User::factory()->count(3)->create();
    $searcher = User::factory()->create();

    $response = $this->actingAs($searcher)->getJson(route('users.search'));

    $response->assertOk();
    // Sin filtro devuelve hasta 5 usuarios
    expect(count($response->json('data')))->toBeLessThanOrEqual(5);
});

test('search with no matches returns empty array', function () {
    $searcher = User::factory()->create();

    $response = $this->actingAs($searcher)->getJson(route('users.search', ['search' => 'zzzznonexistent']));

    $response->assertOk();
    $response->assertJsonCount(0, 'data');
});

test('unauthenticated user cannot search users', function () {
    $response = $this->getJson(route('users.search', ['search' => 'test']));

    $response->assertStatus(401);
});
