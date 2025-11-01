<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Curso;
use App\Models\Inscripcion;
use App\Models\Rol;
use App\Models\Modulo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function dashboard()
    {
        if (!session('usuario')) {
            return redirect('/login')->with('error', 'Debes iniciar sesión primero.');
        }

        $usuario = session('usuario');
        $roles = session('user_roles', []);

        if (!in_array('Administrador', $roles)) {
            return redirect('/login')->with('error', 'No tienes acceso a esta área.');
        }

        try {
            // Estadísticas generales
            $total_estudiantes = Usuario::whereHas('roles', function($query) {
                $query->where('Nombre', 'Estudiante');
            })->count();
            
            $total_docentes = Usuario::whereHas('roles', function($query) {
                $query->where('Nombre', 'Docente');
            })->count();
            
            $total_cursos = Curso::count();
            $total_inscripciones = Inscripcion::count();

            // Cursos más populares - CORREGIDO con nombres correctos
            $cursos_populares = Curso::withCount(['inscripciones as inscripciones_count'])
                ->orderBy('inscripciones_count', 'desc')
                ->limit(5)
                ->get();

            // Últimos estudiantes registrados
            $nuevos_estudiantes = Usuario::whereHas('roles', function($query) {
                $query->where('Nombre', 'Estudiante');
            })->orderBy('Fecha_registro', 'desc')
              ->limit(5)
              ->get();

        } catch (\Exception $e) {
            $total_estudiantes = 0;
            $total_docentes = 0;
            $total_cursos = 0;
            $total_inscripciones = 0;
            $cursos_populares = collect();
            $nuevos_estudiantes = collect();
        }

        return view('admin.dashboard', [
            'usuario' => $usuario,
            'user_roles' => $roles,
            'total_estudiantes' => $total_estudiantes,
            'total_docentes' => $total_docentes,
            'total_cursos' => $total_cursos,
            'total_inscripciones' => $total_inscripciones,
            'cursos_populares' => $cursos_populares,
            'nuevos_estudiantes' => $nuevos_estudiantes
        ]);
    }

    public function gestionCursos()
    {
        if (!session('usuario')) {
            return redirect('/login');
        }

        $cursos = Curso::all();
        $docentes = Usuario::whereHas('roles', function($query) {
            $query->where('Nombre', 'Docente');
        })->get();

        return view('admin.gestion_cursos', [
            'usuario' => session('usuario'),
            'cursos' => $cursos,
            'docentes' => $docentes
        ]);
    }

    public function asignarDocenteCurso(Request $request)
    {
        if (!session('usuario')) {
            return redirect('/login');
        }

        $request->validate([
            'Id_curso' => 'required|exists:cursos,Id_curso',
            'Id_docente' => 'required|exists:usuarios,Id_usuario'
        ]);

        try {
            // Verificar si ya está asignado
            $asignacionExistente = DB::table('docente_curso')->where([
                'Id_curso' => $request->Id_curso,
                'Id_docente' => $request->Id_docente
            ])->first();

            if ($asignacionExistente) {
                return back()->with('error', 'Este docente ya está asignado a este curso.');
            }

            // Crear asignación
            DB::table('docente_curso')->insert([
                'Id_curso' => $request->Id_curso,
                'Id_docente' => $request->Id_docente,
                'Fecha_asignacion' => now()->format('Y-m-d')
            ]);

            return back()->with('success', 'Docente asignado al curso exitosamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al asignar docente: ' . $e->getMessage());
        }
    }

    public function estudiantesPorCurso($Id_curso)
{
    if (!session('usuario')) {
        return redirect('/login')->with('error', 'Debes iniciar sesión primero.');
    }

    try {
        // Buscar el curso
        $curso = Curso::find($Id_curso);
        
        if (!$curso) {
            return redirect('/admin/dashboard')->with('error', 'Curso no encontrado.');
        }

        // Obtener estudiantes de forma segura
        $estudiantes = collect(); // Inicializar como colección vacía
        
        try {
            $estudiantes = $curso->estudiantes;
        } catch (\Exception $e) {
            Log::error('Error al cargar estudiantes: ' . $e->getMessage());
            // Si hay error, obtener estudiantes manualmente
            $estudiantes = Usuario::whereHas('inscripciones', function($query) use ($Id_curso) {
                $query->where('Id_curso', $Id_curso);
            })->whereHas('roles', function($query) {
                $query->where('Nombre', 'Estudiante');
            })->get();
        }

        return view('admin.estudiantes_curso', [
            'usuario' => session('usuario'),
            'curso' => $curso,
            'estudiantes' => $estudiantes
        ]);

    } catch (\Exception $e) {
        Log::error('Error en estudiantesPorCurso: ' . $e->getMessage());
        return redirect('/admin/dashboard')->with('error', 'Error al cargar la página: ' . $e->getMessage());
    }
}
    public function docentesPorCurso($Id_curso)
    {
        if (!session('usuario')) {
            return redirect('/login');
        }

        $curso = Curso::find($Id_curso);
        if (!$curso) {
            return back()->with('error', 'Curso no encontrado.');
        }

        // Obtener docentes usando la relación corregida
        $docentes = $curso->docentes;

        return view('admin.docentes_curso', [
            'usuario' => session('usuario'),
            'curso' => $curso,
            'docentes' => $docentes
        ]);
    }

    public function crearCurso(Request $request)
    {
        if (!session('usuario')) {
            return redirect('/login');
        }

        $request->validate([
            'Titulo' => 'required|string|max:255',
            'Descripcion' => 'required|string',
            'Duracion' => 'required|integer',
            'Costo' => 'required|numeric',
            'Modalidad' => 'required|in:Online,Presencial,Mixto'
        ]);

        try {
            Curso::create([
                'Titulo' => $request->Titulo,
                'Descripcion' => $request->Descripcion,
                'Duracion' => $request->Duracion,
                'Costo' => $request->Costo,
                'Modalidad' => $request->Modalidad,
                'Estado' => 1
            ]);

            return back()->with('success', 'Curso creado exitosamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al crear curso: ' . $e->getMessage());
        }
    }

    // NUEVO: Método para asignar rol de docente a un usuario
    public function asignarRolDocente(Request $request)
    {
        if (!session('usuario')) {
            return redirect('/login');
        }

        $request->validate([
            'Id_usuario' => 'required|exists:usuarios,Id_usuario'
        ]);

        try {
            $usuario = Usuario::find($request->Id_usuario);
            $rolDocente = Rol::where('Nombre', 'Docente')->first();

            if (!$rolDocente) {
                return back()->with('error', 'El rol Docente no existe en el sistema.');
            }

            // Verificar si ya es docente
            if ($usuario->esDocente()) {
                return back()->with('error', 'Este usuario ya tiene el rol de docente.');
            }

            // Asignar rol de docente
            $usuario->roles()->attach($rolDocente->Id_rol);

            return back()->with('success', 'Rol de docente asignado exitosamente al usuario.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al asignar rol: ' . $e->getMessage());
        }
    }

    // NUEVO: Método para gestionar usuarios
    public function gestionUsuarios()
    {
        if (!session('usuario')) {
            return redirect('/login');
        }

        $estudiantes = Usuario::whereHas('roles', function($query) {
            $query->where('Nombre', 'Estudiante');
        })->get();

        $docentes = Usuario::whereHas('roles', function($query) {
            $query->where('Nombre', 'Docente');
        })->get();

        $usuariosSinRol = Usuario::whereDoesntHave('roles')->get();

        return view('admin.gestion_usuarios', [
            'usuario' => session('usuario'),
            'estudiantes' => $estudiantes,
            'docentes' => $docentes,
            'usuariosSinRol' => $usuariosSinRol
        ]);
    }
}