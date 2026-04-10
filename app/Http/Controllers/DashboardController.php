<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $estadisticas = [
            [
                'titulo' => 'Pendientes',
                'valor' => $user->tasks()->where('status', 'pending')->count(),
                'icono' => 'clock',
                'color' => 'red',
            ],
            [
                'titulo' => 'En Progreso',
                'valor' => $user->tasks()->where('status', 'in_progress')->count(),
                'icono' => 'play-circle',
                'color' => 'blue',
            ],
            [
                'titulo' => 'Completadas',
                'valor' => $user->tasks()->where('status', 'completed')->count(),
                'icono' => 'check-circle-2',
                'color' => 'green',
            ],
        ];

        return view('pages.dashboard', compact('estadisticas'));
    }
}
