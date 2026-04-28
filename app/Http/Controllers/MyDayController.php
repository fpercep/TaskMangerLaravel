<?php

namespace App\Http\Controllers;

class MyDayController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $fechaHoy = now();
        $dateFormat = config('app.date_format', 'd/m/Y');

        // I-F1: Dos consultas SQL separadas en vez de get() + partition() in-memory.
        // Delega el filtrado al motor de base de datos para escalabilidad.
        $baseQuery = $user->tasks()
            ->with(['project:id,name'])
            ->where('status', '!=', 'completed');

        $tareasAnteriores = (clone $baseQuery)
            ->whereNotNull('due_date')
            ->where('due_date', '<', $fechaHoy->copy()->startOfDay())
            ->orderBy('due_date', 'desc')
            ->get()
            ->map(fn ($task) => $this->formatTask($task, $dateFormat))
            ->values();

        $tareasMasTarde = (clone $baseQuery)
            ->where(function ($q) use ($fechaHoy) {
                $q->whereNull('due_date')
                  ->orWhere('due_date', '>=', $fechaHoy->copy()->startOfDay());
            })
            ->orderBy('due_date', 'asc')
            ->get()
            ->map(fn ($task) => $this->formatTask($task, $dateFormat))
            ->values();

        // I-F3: Formato de fecha centralizado desde config
        $fechaHoyStr = $fechaHoy->format($dateFormat);

        return view('pages.my-day', [
            'fechaHoy' => $fechaHoyStr,
            'tareasMasTarde' => $tareasMasTarde,
            'tareasAnteriores' => $tareasAnteriores,
        ]);
    }

    private function formatTask($task, string $dateFormat): array
    {
        return [
            'titulo' => $task->name,
            'proyecto' => $task->project?->name ?? 'Sin Proyecto',
            'fecha' => $task->due_date ? $task->due_date->format($dateFormat) : 'Sin fecha',
        ];
    }
}
