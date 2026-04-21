<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $taskCounts = $user->tasks()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $estadisticas = [
            [
                'titulo' => 'Pendientes',
                'valor' => $taskCounts->get('pending', 0),
                'icono' => 'clock',
                'color' => 'red',
            ],
            [
                'titulo' => 'En Progreso',
                'valor' => $taskCounts->get('in_progress', 0),
                'icono' => 'play-circle',
                'color' => 'blue',
            ],
            [
                'titulo' => 'Completadas',
                'valor' => $taskCounts->get('completed', 0),
                'icono' => 'check-circle-2',
                'color' => 'green',
            ],
        ];

        return view('pages.dashboard', compact('estadisticas'));
    }
}
