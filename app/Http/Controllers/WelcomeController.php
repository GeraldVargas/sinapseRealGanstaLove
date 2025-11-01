<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Curso;
use App\Models\Inscripcion;

class WelcomeController extends Controller
{
    public function index()
    {
        // Si el usuario ya está autenticado, redirigir según su rol en SESION
        if (session()->has('usuario')) {
            $usuario = session('usuario');
            $roles = session('user_roles', []); // Obtener roles de la sesión
            
            // Redirigir según el rol de la sesión
            if (in_array('Estudiante', $roles)) {
                return redirect('/estudiante/dashboard');
            } elseif (in_array('Docente', $roles)) {
                return redirect('/docente/dashboard');
            } elseif (in_array('Administrador', $roles)) {
                return redirect('/admin/dashboard');
            }
        }
        
        // Obtener todas las variables que necesita la vista welcome
        try {
            // Estadísticas
            $estadisticas = [
                'total_estudiantes' => Usuario::where('Estado', true)->count(),
                'total_cursos' => Curso::where('Estado', true)->count(),
                'total_inscripciones' => Inscripcion::where('Estado', true)->count(),
                'total_docentes' => 25, // Valor temporal
            ];
            
            // Cursos destacados (los 3 cursos más recientes o con más inscripciones)
            $cursos_destacados = Curso::where('Estado', true)
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();
                
            // Si no hay cursos, crear datos de ejemplo
            if ($cursos_destacados->count() === 0) {
                $cursos_destacados = $this->getCursosEjemplo();
            }
            
        } catch (\Exception $e) {
            // En caso de error, usar valores por defecto
            $estadisticas = [
                'total_estudiantes' => 1000,
                'total_cursos' => 50,
                'total_inscripciones' => 5000,
                'total_docentes' => 25,
            ];
            
            $cursos_destacados = $this->getCursosEjemplo();
        }
        
        // Si no está autenticado, mostrar la página de bienvenida con todos los datos
        return view('welcome', compact('estadisticas', 'cursos_destacados'));
    }
    
    private function getCursosEjemplo()
    {
        // Datos de ejemplo para desarrollo
        return collect([
            (object)[
                'Titulo' => 'Programación Web con Laravel',
                'Descripcion' => 'Aprende a desarrollar aplicaciones web modernas con el framework Laravel y PHP.',
                'Duracion' => 40,
                'Costo' => 0,
                'Modalidad' => 'Online'
            ],
            (object)[
                'Titulo' => 'Machine Learning para Principiantes',
                'Descripcion' => 'Introducción al machine learning con Python y las principales bibliotecas de IA.',
                'Duracion' => 30,
                'Costo' => 49.99,
                'Modalidad' => 'Online'
            ],
            (object)[
                'Titulo' => 'Diseño UX/UI Avanzado',
                'Descripcion' => 'Domina las técnicas avanzadas de diseño de experiencia e interfaz de usuario.',
                'Duracion' => 35,
                'Costo' => 29.99,
                'Modalidad' => 'Mixto'
            ]
        ]);
    }
}