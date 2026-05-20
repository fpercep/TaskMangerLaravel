<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    // Definir una ruta temporal protegida por el middleware para la prueba
    Route::get('/_test_admin', function () {
        return 'success';
    })->middleware(['web', 'auth', 'super_admin']);
});

test('non-authenticated users are blocked by super admin middleware', function () {
    $response = $this->get('/_test_admin');
    
    // Debería redirigir al login (gracias al middleware auth)
    $response->assertRedirect('/login');
});

test('regular authenticated users are blocked by super admin middleware', function () {
    $user = User::factory()->create(['is_super_admin' => false]);

    $response = $this->actingAs($user)->get('/_test_admin');

    // Debe arrojar un 403 Forbidden
    $response->assertStatus(403);
});

test('super admin users are allowed by super admin middleware', function () {
    $user = User::factory()->create(['is_super_admin' => true]);

    $response = $this->actingAs($user)->get('/_test_admin');

    // Debe permitir el acceso (200 OK)
    $response->assertOk();
    $response->assertSee('success');
});
