<?php

namespace App\Http\Controllers;

class MiDiaController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $fechaHoy = now();

        // Tareas pendientes con fecha de hoy o futura (o sin fecha)
        $tareasMasTarde = $user->tasks()
            ->with(['project'])
            ->where('status', '!=', 'completed')
            ->where(function ($query) use ($fechaHoy) {
                $query->whereNull('due_date')
                    ->orWhere('due_date', '>=', $fechaHoy->startOfDay());
            })
            ->get()
            ->map($this->formatTask(...));

        // Tareas pendientes con fecha pasada
        $tareasAnteriores = $user->tasks()
            ->with(['project'])
            ->where('status', '!=', 'completed')
            ->where('due_date', '<', $fechaHoy->startOfDay())
            ->get()
            ->map($this->formatTask(...));

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
