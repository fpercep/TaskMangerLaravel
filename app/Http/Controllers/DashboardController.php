<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // I-F2: Caché de 5 minutos para estadísticas agregadas.
        // Invalidar desde TaskController al cambiar estado.
        $taskCounts = Cache::remember(
            "dashboard_stats_{$user->id}",
            now()->addMinutes(5),
            function () use ($user) {
                return $user->tasks()
                    ->selectRaw('status, count(*) as total')
                    ->groupBy('status')
                    ->pluck('total', 'status');
            }
        );

        // I-F4: Clases completas pre-resueltas en vez de interpolación dinámica.
        // Tailwind necesita clases completas para detección en compilación/purge.
        $estadisticas = [
            [
                'titulo' => 'Pendientes',
                'valor' => $taskCounts->get('pending', 0),
                'icono' => 'clock',
                'bg_class' => 'bg-red-50',
                'text_class' => 'text-red-500',
            ],
            [
                'titulo' => 'En Progreso',
                'valor' => $taskCounts->get('in_progress', 0),
                'icono' => 'play-circle',
                'bg_class' => 'bg-blue-50',
                'text_class' => 'text-blue-500',
            ],
            [
                'titulo' => 'Completadas',
                'valor' => $taskCounts->get('completed', 0),
                'icono' => 'check-circle-2',
                'bg_class' => 'bg-green-50',
                'text_class' => 'text-green-500',
            ],
        ];

        return view('pages.dashboard', compact('estadisticas'));
    }
}
