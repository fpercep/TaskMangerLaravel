<?php

namespace App\Http\Controllers;

class MiDiaController extends Controller
{
    public function index()
    {
        $fechaHoy = date('d/m/Y');

        $tareasMasTarde = [
            ['titulo' => 'Revisar documentación API', 'equipo' => 'Equipo 1', 'proyecto' => 'Proyecto Alpha', 'fecha' => '05/02/2026'],
            ['titulo' => 'Implementar autenticación', 'equipo' => 'Equipo 1', 'proyecto' => 'Rediseño Web', 'fecha' => '06/02/2026'],
            ['titulo' => 'Diseñar mockups dashboard', 'equipo' => 'Equipo 2', 'proyecto' => 'Campaña Verano', 'fecha' => '07/02/2026'],
        ];

        $tareasAnteriores = [
            ['titulo' => 'Configurar base de datos', 'equipo' => 'Equipo 1', 'proyecto' => 'Proyecto Alpha', 'fecha' => '01/02/2026'],
            ['titulo' => 'Revisar wireframes', 'equipo' => 'Equipo 2', 'proyecto' => 'Rediseño Web', 'fecha' => '31/01/2026'],
            ['titulo' => 'Preparar presentación', 'equipo' => 'Equipo 1', 'proyecto' => 'Campaña Verano', 'fecha' => '30/01/2026'],
        ];

        return view('pages.mi-dia', compact('fechaHoy', 'tareasMasTarde', 'tareasAnteriores'));
    }
}
