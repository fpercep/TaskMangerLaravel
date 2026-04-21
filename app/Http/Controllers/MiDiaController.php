<?php

namespace App\Http\Controllers;

class MiDiaController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $fechaHoy = now();

        $startOfDay = $fechaHoy->copy()->startOfDay();

        // Obtener todas las tareas no completadas con su proyecto
        $pendientes = $user->tasks()
            ->with(['project'])
            ->where('status', '!=', 'completed')
            ->get();

        // Particionar en memoria (true -> anteriores, false -> más tarde)
        [$tareasAnteriores, $tareasMasTarde] = $pendientes->partition(function ($task) use ($startOfDay) {
            return $task->due_date && $task->due_date < $startOfDay;
        });

        // Formatear
        $tareasMasTarde = $tareasMasTarde->map($this->formatTask(...))->values();
        $tareasAnteriores = $tareasAnteriores->map($this->formatTask(...))->values();

        $fechaHoyStr = $fechaHoy->format('d/m/Y');

        return view('pages.mi-dia', [
            'fechaHoy' => $fechaHoyStr,
            'tareasMasTarde' => $tareasMasTarde,
            'tareasAnteriores' => $tareasAnteriores,
        ]);
    }

    private function formatTask($task)
    {
        return [
            'titulo' => $task->name,
            'proyecto' => $task->project?->name ?? 'Sin Proyecto',
            'fecha' => $task->due_date ? $task->due_date->format('d/m/Y') : 'Sin fecha',
        ];
    }
}
