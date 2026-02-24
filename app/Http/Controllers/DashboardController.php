<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        $estadisticas = [
            [
                'titulo' => 'Pendientes',
                'valor' => 15,
                'icono' => 'clock',
                'color' => 'red',
            ],
            [
                'titulo' => 'Por Revisar',
                'valor' => 10,
                'icono' => 'file-search',
                'color' => 'blue',
            ],
            [
                'titulo' => 'Completadas',
                'valor' => 12,
                'icono' => 'check-circle-2',
                'color' => 'green',
            ],
        ];

        return view('pages.dashboard', compact('estadisticas'));
    }
}
