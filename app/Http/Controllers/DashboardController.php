<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Curso;
use App\Models\Rol;
use App\Models\Insignia;

class DashboardController extends Controller
{
    public function index()
    {
        // Datos REALES de la BD
        try {
            $total_estudiantes = Usuario::whereHas('roles', function($query) {
                $query->where('Nombre', 'Estudiante');
            })->count();
            
            $total_docentes = Usuario::whereHas('roles', function($query) {
                $query->where('Nombre', 'Docente');
            })->count();
            
            $total_cursos = Curso::count();
            $total_insignias = Insignia::count();
            $cursos_destacados = Curso::limit(3)->get();

        } catch (\Exception $e) {
            // Si hay error, usar datos de ejemplo
            $total_estudiantes = 0;
            $total_docentes = 0;
            $total_cursos = 0;
            $total_insignias = 0;
            $cursos_destacados = collect();
        }

        $estadisticas = [
            'total_estudiantes' => $total_estudiantes,
            'total_docentes' => $total_docentes,
            'total_cursos' => $total_cursos,
            'total_insignias' => $total_insignias
        ];

        return view('dashboard', compact('estadisticas', 'cursos_destacados'));
    }

    public function cursos()
    {
        $cursos = Curso::all();
        return view('cursos', compact('cursos'));
    }

    public function perfil()
    {
        return view('perfil');
    }
}