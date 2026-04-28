<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Muestra la vista unificada de ajustes con tabs (Mi Perfil / Configuración).
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // A-F7: Transformar modelo a array limpio antes de pasar a la vista.
        // A-F2: Formateo de fecha procesado en backend, no en Blade.
        $userData = [
            'name' => $user->name,
            'email' => $user->email,
            'initials' => $user->initials,
            'member_since' => $user->created_at->translatedFormat('M Y'),
            'has_verified_email' => $user->hasVerifiedEmail(),
            'must_verify_email' => $user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail,
        ];

        return view('pages.settings.index', [
            'user' => $user,         // Necesario para los formularios POST de Breeze (old() / fill)
            'userData' => $userData,  // Datos limpios para presentación
        ]);
    }
}
