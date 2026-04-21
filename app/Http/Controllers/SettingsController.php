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
        return view('pages.settings.index', [
            'user' => $request->user(),
        ]);
    }
}
