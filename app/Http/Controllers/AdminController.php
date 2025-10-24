<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Curso;

class AdminController extends Controller
{
    public function index()
    {
        try {
            $usuarios_totales = Usuario::count();
            $cursos_activos = Curso::count();
            $usuarios_recientes = Usuario::orderBy('Id_usuario', 'desc')->limit(5)->get();
        } catch (\Exception $e) {
            $usuarios_totales = 0;
            $cursos_activos = 0;
            $usuarios_recientes = collect();
        }

        $estadisticas_admin = [
            'usuarios_totales' => $usuarios_totales,
            'cursos_activos' => $cursos_activos,
            'ingresos_mensuales' => 12500,
            'nuevos_estudiantes' => 23
        ];

        return view('admin.dashboard', compact('estadisticas_admin', 'usuarios_recientes'));
    }
}