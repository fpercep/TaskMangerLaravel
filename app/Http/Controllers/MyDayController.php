<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class MyDayController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $hoy = now()->startOfDay();
        $dateFormat = config('app.date_format', 'd/m/Y');

        // Query base: tareas asignadas al usuario, no completadas, con proyecto
        $baseQuery = $user->tasks()
            ->with(['project:id,name'])
            ->where('status', '!=', 'completed');

        // Tareas de hoy (panel principal): due_date = hoy
        $tareasHoy = (clone $baseQuery)
            ->whereDate('due_date', $hoy)
            ->orderBy('priority', 'asc')
            ->get()
            ->map(fn ($task) => $this->formatTaskFull($task, $dateFormat))
            ->values();

        // Incluir también las completadas hoy para mostrar progreso
        $tareasHoyCompletadas = $user->tasks()
            ->with(['project:id,name'])
            ->where('status', 'completed')
            ->whereDate('due_date', $hoy)
            ->get()
            ->map(fn ($task) => $this->formatTaskFull($task, $dateFormat))
            ->values();

        $tareasHoy = $tareasHoy->concat($tareasHoyCompletadas)->values();

        // Sugerencias: Más Tarde (sin fecha o fecha futura)
        $tareasMasTarde = (clone $baseQuery)
            ->where(function ($q) use ($hoy) {
                $q->whereNull('due_date')
                  ->orWhere('due_date', '>', $hoy);
            })
            ->orderBy('due_date', 'asc')
            ->get()
            ->map(fn ($task) => $this->formatTaskFull($task, $dateFormat))
            ->values();

        // Sugerencias: Anteriores (fecha pasada, no completadas)
        $tareasAnteriores = (clone $baseQuery)
            ->whereNotNull('due_date')
            ->where('due_date', '<', $hoy)
            ->orderBy('due_date', 'desc')
            ->get()
            ->map(fn ($task) => $this->formatTaskFull($task, $dateFormat))
            ->values();

        // Proyectos del usuario para el filtro
        $proyectos = $user->projects()
            ->select('projects.id', 'projects.name')
            ->get()
            ->map(fn ($p) => ['id' => $p->id, 'name' => $p->name])
            ->values();

        // I-F3: Formato de fecha centralizado desde config
        $fechaHoyStr = now()->format($dateFormat);

        return view('pages.my-day', [
            'fechaHoy'         => $fechaHoyStr,
            'tareasHoy'        => $tareasHoy,
            'tareasMasTarde'   => $tareasMasTarde,
            'tareasAnteriores' => $tareasAnteriores,
            'proyectos'        => $proyectos,
        ]);
    }

    /**
     * Formato completo de tarea con todos los campos necesarios para Alpine.
     * Reutiliza la relación project ya cargada via eager loading.
     */
    private function formatTaskFull($task, string $dateFormat): array
    {
        return [
            'id'           => $task->id,
            'name'         => $task->name,
            'status'       => $task->status,
            'priority'     => $task->priority,
            'due_date'     => $task->due_date?->format('Y-m-d'),
            'due_date_fmt' => $task->due_date ? $task->due_date->format($dateFormat) : 'Sin fecha',
            'project_id'   => $task->project_id,
            'project_name' => $task->project?->name ?? 'Sin Proyecto',
        ];
    }
}
