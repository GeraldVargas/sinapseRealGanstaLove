<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Curso;
use App\Models\Usuario;
use App\Models\Inscripcion;
use App\Models\Modulo;
use App\Models\Tema;
use App\Models\Evaluacion;
use App\Models\ProgresoCurso;

class DocenteController extends Controller
{
    public function dashboard()
    {
        if (!session('usuario')) {
            return redirect('/login')->with('error', 'Debes iniciar sesión primero.');
        }

        $usuario = session('usuario');
        $roles = session('user_roles', []);

        if (!in_array('Docente', $roles)) {
            return redirect('/login')->with('error', 'No tienes acceso a esta área.');
        }

        try {
            // Obtener cursos asignados al docente (por ahora todos los cursos)
            $cursos_asignados = Curso::all();
            
            // Estadísticas del docente
            $total_estudiantes = Inscripcion::distinct('id_usuario')->count();
            $total_cursos = $cursos_asignados->count();
            $evaluaciones_pendientes = Evaluacion::count();

        } catch (\Exception $e) {
            $cursos_asignados = collect();
            $total_estudiantes = 0;
            $total_cursos = 0;
            $evaluaciones_pendientes = 0;
        }

        return view('docente.dashboard', [
            'usuario' => $usuario,
            'user_roles' => $roles,
            'cursos_asignados' => $cursos_asignados,
            'total_estudiantes' => $total_estudiantes,
            'total_cursos' => $total_cursos,
            'evaluaciones_pendientes' => $evaluaciones_pendientes
        ]);
    }

    public function verCurso($idCurso)
    {
        if (!session('usuario')) {
            return redirect('/login');
        }

        $curso = Curso::find($idCurso);
        if (!$curso) {
            return back()->with('error', 'Curso no encontrado.');
        }

        // Obtener estudiantes inscritos en el curso
        $estudiantes = Usuario::whereHas('inscripciones', function($query) use ($idCurso) {
            $query->where('id_curso', $idCurso);
        })->whereHas('roles', function($query) {
            $query->where('Nombre', 'Estudiante');
        })->get();

        // Obtener todos los estudiantes para poder agregar
        $todos_estudiantes = Usuario::whereHas('roles', function($query) {
            $query->where('Nombre', 'Estudiante');
        })->whereDoesntHave('inscripciones', function($query) use ($idCurso) {
            $query->where('id_curso', $idCurso);
        })->get();

        // Obtener módulos del curso
        $modulos = Modulo::where('id_curso', $idCurso)->get();

        // Obtener evaluaciones del curso
        $evaluaciones = Evaluacion::whereHas('tema.modulo', function($query) use ($idCurso) {
            $query->where('id_curso', $idCurso);
        })->get();

        return view('docente.curso_detalle', [
            'usuario' => session('usuario'),
            'curso' => $curso,
            'estudiantes' => $estudiantes,
            'todos_estudiantes' => $todos_estudiantes,
            'modulos' => $modulos,
            'evaluaciones' => $evaluaciones
        ]);
    }

    public function agregarEstudiante(Request $request, $idCurso)
    {
        if (!session('usuario')) {
            return redirect('/login');
        }

        $request->validate([
            'id_estudiante' => 'required|exists:usuarios,Id_usuario'
        ]);

        try {
            // Verificar si ya está inscrito
            $inscripcionExistente = Inscripcion::where('id_usuario', $request->id_estudiante)
                ->where('id_curso', $idCurso)
                ->first();

            if ($inscripcionExistente) {
                return back()->with('error', 'El estudiante ya está inscrito en este curso.');
            }

            // Crear nueva inscripción
            Inscripcion::create([
                'id_usuario' => $request->id_estudiante,
                'id_curso' => $idCurso,
                'Fecha_inscripcion' => now()->format('Y-m-d'),
                'Estado' => 1
            ]);

            // Crear progreso inicial para el estudiante
            ProgresoCurso::create([
                'id_curso' => $idCurso,
                'id_Usuario' => $request->id_estudiante,
                'Fecha_actualizacion' => now()->format('Y-m-d'),
                'Porcentaje' => 0,
                'Nivel' => 'Principiante',
                'Modulos_Com' => 0,
                'Temas_Comple' => 0,
                'Evaluaciones' => 0,
                'Actividades_R' => 0
            ]);

            return back()->with('success', 'Estudiante agregado al curso exitosamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al agregar estudiante: ' . $e->getMessage());
        }
    }

    public function eliminarEstudiante($idCurso, $idEstudiante)
    {
        if (!session('usuario')) {
            return redirect('/login');
        }

        try {
            // Eliminar inscripción
            $inscripcion = Inscripcion::where('id_usuario', $idEstudiante)
                ->where('id_curso', $idCurso)
                ->first();

            if ($inscripcion) {
                $inscripcion->delete();
            }

            // Eliminar progreso del curso
            $progreso = ProgresoCurso::where('id_Usuario', $idEstudiante)
                ->where('id_curso', $idCurso)
                ->first();

            if ($progreso) {
                $progreso->delete();
            }

            return back()->with('success', 'Estudiante eliminado del curso exitosamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar estudiante: ' . $e->getMessage());
        }
    }

    public function verDetalleEstudiante($idCurso, $idEstudiante)
    {
        if (!session('usuario')) {
            return redirect('/login');
        }

        $curso = Curso::find($idCurso);
        $estudiante = Usuario::find($idEstudiante);

        if (!$curso || !$estudiante) {
            return back()->with('error', 'Curso o estudiante no encontrado.');
        }

        // Obtener progreso del estudiante en el curso
        $progreso = ProgresoCurso::where('id_Usuario', $idEstudiante)
            ->where('id_curso', $idCurso)
            ->first();

        // Obtener insignias del estudiante
        $insignias = $estudiante->insignias;

        return view('docente.estudiante_detalle', [
            'usuario' => session('usuario'),
            'curso' => $curso,
            'estudiante' => $estudiante,
            'progreso' => $progreso,
            'insignias' => $insignias
        ]);
    }

    public function crearEvaluacion(Request $request, $idCurso)
    {
        if (!session('usuario')) {
            return redirect('/login');
        }

        $request->validate([
            'id_tema' => 'required|exists:temas,Id_tema',
            'tipo' => 'required|string',
            'puntaje_maximo' => 'required|integer',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date'
        ]);

        try {
            Evaluacion::create([
                'id_tema' => $request->id_tema,
                'Tipo' => $request->tipo,
                'Puntaje_maximo' => $request->puntaje_maximo,
                'Fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin
            ]);

            return back()->with('success', 'Evaluación creada exitosamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al crear la evaluación: ' . $e->getMessage());
        }
    }
}