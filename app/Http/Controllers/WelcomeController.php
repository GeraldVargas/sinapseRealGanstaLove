<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Curso;
use App\Models\Usuario;

class WelcomeController extends Controller
{
    public function index()
    {
        $estadisticas = [
            'total_estudiantes' => Usuario::whereHas('roles', function($query) {
                $query->where('Nombre', 'Estudiante');
            })->count(),
            'total_cursos' => Curso::count(),
            'total_docentes' => Usuario::whereHas('roles', function($query) {
                $query->where('Nombre', 'Docente');
            })->count(),
        ];

        $cursos_destacados = Curso::limit(3)->get();

        return view('welcome', compact('estadisticas', 'cursos_destacados'));
    }
}