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
use Illuminate\Support\Facades\DB;
use App\Models\Inscripciones;
use Illuminate\Support\Facades\Log;
use App\Models\Entrega;
use App\Models\ProgresoTema;
use App\Models\Pago;
use App\Models\ProgresoEvaluacion;


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
//aqui
public function verCurso($idCurso)
{
    if (!session('usuario')) {
        return redirect('/login');
    }

    $curso = Curso::find($idCurso);
    if (!$curso) {
        return back()->with('error', 'Curso no encontrado.');
    }

    try {
        // Obtener estudiantes inscritos en el curso
        // En verCurso(), cambia esta línea:
$estudiantes = DB::select("
    SELECT 
        u.Id_usuario,
        u.Nombre,
        u.Apellido,
        u.Email,
        i.Fecha_inscripcion,
        pc.Porcentaje as progreso,
        pc.Modulos_completados,
        pc.Temas_completados
    FROM usuarios u
    INNER JOIN inscripciones i ON u.Id_usuario = i.Id_usuario
    LEFT JOIN progreso_curso pc ON u.Id_usuario = pc.Id_Usuario AND pc.Id_curso = ?
    WHERE i.Id_curso = ? 
    AND i.Estado = 1  -- ← SOLO INSCRIPCIONES ACTIVAS
    ORDER BY u.Nombre, u.Apellido
", [$idCurso, $idCurso]);

        // Obtener todos los estudiantes disponibles para agregar
        $todos_estudiantes = DB::select("
            SELECT 
                u.Id_usuario,
                u.Nombre,
                u.Apellido,
                u.Email
            FROM usuarios u
            INNER JOIN rol_usuario ru ON u.Id_usuario = ru.Id_usuario
            INNER JOIN roles r ON ru.Id_rol = r.Id_rol
            WHERE r.Nombre = 'Estudiante'
            AND u.Id_usuario NOT IN (
                SELECT i.Id_usuario 
                FROM inscripciones i 
                WHERE i.Id_curso = ? AND i.Estado = 1
            )
            ORDER BY u.Nombre, u.Apellido
        ", [$idCurso]);

        // Obtener módulos del curso con conteos usando consultas directas
        $modulos = DB::select("
            SELECT 
                m.Id_modulo,
                m.Nombre,
                m.Descripcion,
                m.Id_curso,
                (SELECT COUNT(*) FROM temas t WHERE t.Id_modulo = m.Id_modulo) as total_temas,
                (SELECT COUNT(*) FROM evaluaciones e WHERE e.Id_modulo = m.Id_modulo) as total_evaluaciones
            FROM modulos m
            WHERE m.Id_curso = ?
            ORDER BY m.Id_modulo
        ", [$idCurso]);

        // Obtener temas para cada módulo
        foreach ($modulos as $modulo) {
            $modulo->temas = DB::select("
                SELECT 
                    t.Id_tema,
                    t.Nombre,
                    t.Descripcion,
                    t.Contenido,
                    t.Orden
                FROM temas t
                WHERE t.Id_modulo = ?
                ORDER BY t.Orden
            ", [$modulo->Id_modulo]);
        }

        // Obtener evaluaciones del curso
        $evaluaciones = DB::select("
            SELECT 
                e.Id_evaluacion,
                e.Tipo,
                e.Puntaje_maximo,
                e.Fecha_inicio,
                e.Fecha_fin,
                e.Id_modulo,
                m.Nombre as modulo_nombre
            FROM evaluaciones e
            INNER JOIN modulos m ON e.Id_modulo = m.Id_modulo
            WHERE m.Id_curso = ?
            ORDER BY e.Fecha_inicio
        ", [$idCurso]);

        // 🆕 NUEVO: Obtener entregas pendientes del curso
        $entregasPendientes = DB::select("
            SELECT 
                ent.Id_entrega,
                ent.Id_evaluacion,
                ent.Id_usuario,
                ent.Archivo,
                ent.Descripcion,
                ent.Fecha_entrega,
                ent.Estado,
                u.Nombre as estudiante_nombre,
                u.Apellido as estudiante_apellido,
                e.Tipo as evaluacion_tipo,
                e.Puntaje_maximo,
                m.Nombre as modulo_nombre,
                m.Id_modulo
            FROM entregas ent
            INNER JOIN evaluaciones e ON ent.Id_evaluacion = e.Id_evaluacion
            INNER JOIN modulos m ON e.Id_modulo = m.Id_modulo
            INNER JOIN usuarios u ON ent.Id_usuario = u.Id_usuario
            WHERE m.Id_curso = ?
            AND ent.Estado = 'pendiente'
            ORDER BY ent.Fecha_entrega ASC
        ", [$idCurso]);

        $entregasPendientesCount = count($entregasPendientes);

        // Convertir a colecciones para usar en la vista
        $estudiantes = collect($estudiantes);
        $todos_estudiantes = collect($todos_estudiantes);
        $modulos = collect($modulos);
        $evaluaciones = collect($evaluaciones);
        $entregasPendientes = collect($entregasPendientes);

        return view('docente.curso_detalle', [
            'usuario' => session('usuario'),
            'curso' => $curso,
            'estudiantes' => $estudiantes,
            'todos_estudiantes' => $todos_estudiantes,
            'modulos' => $modulos,
            'evaluaciones' => $evaluaciones,
            'entregasPendientes' => $entregasPendientes, // 🆕 NUEVO
            'entregasPendientesCount' => $entregasPendientesCount // 🆕 NUEVO
        ]);

    } catch (\Exception $e) {
        Log::error('ERROR en verCurso: ' . $e->getMessage());
        return back()->with('error', 'Error al cargar el curso: ' . $e->getMessage());
    }
}
    // En DocenteController - método agregarEstudiante() - CORREGIDO
public function agregarEstudiante($idCurso, Request $request)
{
    try {
        $idEstudiante = $request->input('id_estudiante');
        
        // Verificar si ya está inscrito
        $inscripcionExistente = Inscripcion::where('Id_curso', $idCurso)
            ->where('Id_usuario', $idEstudiante)
            ->first();
            
        if ($inscripcionExistente) {
            return back()->with('error', 'El estudiante ya está inscrito en este curso.');
        }
        
        // Crear nueva inscripción
        Inscripcion::create([
            'Id_curso' => $idCurso,
            'Id_usuario' => $idEstudiante,
            'Fecha_inscripcion' => now(),
            'Estado' => true
        ]);
        
        return back()->with('success', 'Estudiante agregado al curso exitosamente.');
        
    } catch (\Exception $e) {
        Log::error('Error al agregar estudiante: ' . $e->getMessage());
        return back()->with('error', 'Error al agregar estudiante: ' . $e->getMessage());
    }
}
    
  //aqui

// En DocenteController - método eliminarEstudiante CORREGIDO
public function eliminarEstudiante($idCurso, $idEstudiante)
{
    DB::beginTransaction();
    
    try {
        // Buscar la inscripción
        $inscripcion = DB::table('inscripciones')
            ->where('Id_curso', $idCurso)
            ->where('Id_usuario', $idEstudiante)
            ->first();
            
        if (!$inscripcion) {
            return back()->with('error', 'No se encontró la inscripción del estudiante.');
        }
        
        $idInscripcion = $inscripcion->Id_inscripcion;
        
        Log::info("🔧 Eliminando estudiante - Curso: $idCurso, Estudiante: $idEstudiante, Inscripción: $idInscripcion");

        // 1. ACTUALIZAR CUPOS DEL CURSO (incrementar)
        $curso = DB::table('cursos')->where('Id_curso', $idCurso)->first();
        if ($curso && isset($curso->Cupos_disponibles)) {
            $nuevosCupos = $curso->Cupos_disponibles + 1;
            $estadoCupos = $nuevosCupos > 0 ? 'disponible' : 'completo';
            
            DB::table('cursos')
                ->where('Id_curso', $idCurso)
                ->update([
                    'Cupos_disponibles' => $nuevosCupos,
                    'Estado_cupos' => $estadoCupos
                ]);
            
            Log::info("✅ Cupos actualizados: +1 = $nuevosCupos disponibles");
        }

        // 2. ELIMINAR O ACTUALIZAR gestion_puntos (CORREGIDO)
        // En lugar de eliminar, actualizamos el Id_inscripcion a NULL
        DB::table('gestion_puntos')
            ->where('Id_inscripcion', $idInscripcion)
            ->update(['Id_inscripcion' => null]);

        Log::info("✅ gestion_puntos actualizado (Id_inscripcion = NULL)");

        // 3. Eliminar progreso_curso
        $progresoEliminado = DB::table('progreso_curso')
            ->where('Id_curso', $idCurso)
            ->where('Id_usuario', $idEstudiante)
            ->delete();
        
        Log::info("✅ Progreso_curso eliminado: $progresoEliminado registros");

        // 4. Eliminar entregas del estudiante en este curso
        $entregasEliminadas = DB::table('entregas')
            ->where('Id_usuario', $idEstudiante)
            ->whereIn('Id_evaluacion', function($query) use ($idCurso) {
                $query->select('e.Id_evaluacion')
                      ->from('evaluaciones as e')
                      ->join('modulos as m', 'e.Id_modulo', '=', 'm.Id_modulo')
                      ->where('m.Id_curso', $idCurso);
            })->delete();
        
        Log::info("✅ Entregas eliminadas: $entregasEliminadas registros");

        // 5. Eliminar progreso_evaluacion del estudiante en este curso
        $progresoEvaluacionEliminado = DB::table('progreso_evaluacion')
            ->where('Id_usuario', $idEstudiante)
            ->whereIn('Id_evaluacion', function($query) use ($idCurso) {
                $query->select('e.Id_evaluacion')
                      ->from('evaluaciones as e')
                      ->join('modulos as m', 'e.Id_modulo', '=', 'm.Id_modulo')
                      ->where('m.Id_curso', $idCurso);
            })->delete();
        
        Log::info("✅ Progreso_evaluacion eliminado: $progresoEvaluacionEliminado registros");

        // 6. Eliminar progreso_tema del estudiante en este curso
        $progresoTemaEliminado = DB::table('progreso_tema')
            ->where('Id_usuario', $idEstudiante)
            ->whereIn('Id_tema', function($query) use ($idCurso) {
                $query->select('t.Id_tema')
                      ->from('temas as t')
                      ->join('modulos as m', 't.Id_modulo', '=', 'm.Id_modulo')
                      ->where('m.Id_curso', $idCurso);
            })->delete();
        
        Log::info("✅ Progreso_tema eliminado: $progresoTemaEliminado registros");

        // 7. Finalmente eliminar la inscripción
        $inscripcionEliminada = DB::table('inscripciones')
            ->where('Id_inscripcion', $idInscripcion)
            ->delete();
        
        Log::info("✅ Inscripción eliminada: $inscripcionEliminada registros");

        DB::commit();
        
        Log::info("🎉 Estudiante eliminado exitosamente del curso");

        return back()->with('success', 'Estudiante eliminado del curso exitosamente.');
        
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('❌ ERROR al eliminar estudiante: ' . $e->getMessage());
        Log::error('Trace: ' . $e->getTraceAsString());
        return back()->with('error', 'Error al eliminar estudiante: ' . $e->getMessage());
    }
}
public function crearEvaluacion(Request $request, $idCurso)
{
    if (!session('usuario')) {
        return redirect('/login');
    }

    $request->validate([
        'Id_modulo' => 'required|exists:modulos,Id_modulo', // CORREGIDO: Id_modulo
        'tipo' => 'required|string',
        'puntaje_maximo' => 'required|integer',
        'fecha_inicio' => 'required|date',
        'fecha_fin' => 'required|date'
    ]);

    try {
        // Verificar que el módulo pertenece al curso
        $modulo = Modulo::where('Id_modulo', $request->Id_modulo)
            ->where('id_curso', $idCurso)
            ->first();

        if (!$modulo) {
            return back()->with('error', 'El módulo no pertenece a este curso.');
        }

        Evaluacion::create([
            'Id_modulo' => $request->Id_modulo, // CORREGIDO: Id_modulo
            'Tipo' => $request->tipo,
            'Puntaje_maximo' => $request->puntaje_maximo,
            'Fecha_inicio' => $request->fecha_inicio,
            'Fecha_fin' => $request->fecha_fin
        ]);

        return back()->with('success', 'Evaluación creada exitosamente.');

    } catch (\Exception $e) {
        return back()->with('error', 'Error al crear la evaluación: ' . $e->getMessage());
    }
}
public function obtenerTemasPorCurso($cursoId)
{
    $temas = Tema::whereHas('modulo', function($query) use ($cursoId) {
        $query->where('id_curso', $cursoId);
    })->get(['Id_tema', 'Nombre']);
    
    return response()->json($temas);
}
public function obtenerModulosPorCurso($cursoId)
{
    $modulos = Modulo::where('id_curso', $cursoId)->get(['Id_modulo', 'Nombre']);
    
    return response()->json($modulos);
}
/**
 * CREAR NUEVO MÓDULO
 */
/**
 * CREAR NUEVO MÓDULO - VERSIÓN CORREGIDA
 */
/**
 * CREAR NUEVO MÓDULO - VERSIÓN CORREGIDA (sin timestamps)
 */
public function crearModulo(Request $request, $idCurso)
{
    if (!session('usuario')) {
        return redirect('/login');
    }

    try {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string'
        ]);

        Log::info("Intentando crear módulo para curso: $idCurso");
        Log::info("Datos recibidos:", $request->all());

        // Verificar que el curso existe usando consulta directa
        $curso = DB::table('cursos')->where('Id_curso', $idCurso)->first();
        if (!$curso) {
            Log::error("Curso no encontrado: $idCurso");
            return back()->with('error', 'Curso no encontrado.');
        }

        // Crear el módulo usando consulta directa - SOLO COLUMNAS EXISTENTES
        DB::table('modulos')->insert([
            'Id_curso' => $idCurso,
            'Nombre' => $request->nombre,
            'Descripcion' => $request->descripcion
            // NO incluir created_at y updated_at si no existen
        ]);

        Log::info("Módulo creado exitosamente para curso: $idCurso");

        return redirect()->route('docente.curso.detalle', $idCurso)
            ->with('success', 'Módulo creado exitosamente.');

    } catch (\Exception $e) {
        Log::error('ERROR al crear módulo: ' . $e->getMessage());
        Log::error('Trace: ' . $e->getTraceAsString());
        return back()->with('error', 'Error al crear el módulo: ' . $e->getMessage());
    }
}
/**
 * ELIMINAR MÓDULO (CON MEJOR MANEJO DE ERRORES)
 */
public function eliminarModulo($idModulo)
{
    if (!session('usuario')) {
        return response()->json(['success' => false, 'message' => 'No autenticado']);
    }

    try {
        $modulo = Modulo::find($idModulo);
        
        if (!$modulo) {
            return response()->json(['success' => false, 'message' => 'Módulo no encontrado']);
        }

        // Verificar si el módulo tiene temas
        if ($modulo->temas()->count() > 0) {
            return response()->json([
                'success' => false, 
                'message' => 'No se puede eliminar el módulo porque tiene temas asociados. Elimina los temas primero.'
            ]);
        }

        // Verificar si el módulo tiene evaluaciones
        if ($modulo->evaluaciones()->count() > 0) {
            return response()->json([
                'success' => false, 
                'message' => 'No se puede eliminar el módulo porque tiene evaluaciones asociadas. Elimina las evaluaciones primero.'
            ]);
        }

        $idCurso = $modulo->Id_curso;
        $modulo->delete();

        return response()->json([
            'success' => true, 
            'message' => 'Módulo eliminado exitosamente.',
            'redirect' => route('docente.curso.detalle', $idCurso) . '#modulos'
        ]);

    } catch (\Exception $e) {
        Log::error('ERROR al eliminar módulo: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Error al eliminar el módulo.']);
    }
}

/**
 * CREAR NUEVO TEMA
 */
/**
 * CREAR NUEVO TEMA - VERSIÓN CORREGIDA
 */
/**
 * CREAR NUEVO TEMA - VERSIÓN CORREGIDA (sin timestamps)
 */
// En DocenteController - método crearTema ACTUALIZADO
// En DocenteController - método crearTema ACTUALIZADO
public function crearTema(Request $request, $idModulo)
{
    if (!session('usuario')) {
        return redirect('/login');
    }

    try {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'contenido_html' => 'nullable|string',
            'tipo_contenido' => 'nullable|in:texto,video,pdf,presentacion',
            'url_video' => 'nullable|url|max:500',
            'archivo_adjunto' => 'nullable|file|max:10240', // 10MB máximo
            'orden' => 'required|integer|min:1'
        ]);

        Log::info("Intentando crear tema para módulo: $idModulo");

        // Verificar que el módulo existe
        $modulo = DB::table('modulos')->where('Id_modulo', $idModulo)->first();
        if (!$modulo) {
            Log::error("Módulo no encontrado: $idModulo");
            return back()->with('error', 'Módulo no encontrado.');
        }

        // Procesar archivo adjunto si se subió
        $nombreArchivo = null;
        if ($request->hasFile('archivo_adjunto')) {
            $archivo = $request->file('archivo_adjunto');
            $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
            
            // Crear directorio si no existe
            $directorio = public_path('storage/temas');
            if (!file_exists($directorio)) {
                mkdir($directorio, 0755, true);
            }
            
            $archivo->move($directorio, $nombreArchivo);
            Log::info("Archivo subido: $nombreArchivo");
        }

        // Crear el tema con todos los campos
        DB::table('temas')->insert([
            'Id_modulo' => $idModulo,
            'Nombre' => $request->nombre,
            'Descripcion' => $request->descripcion,
            'Contenido' => $request->contenido_html, // Para compatibilidad
            'Contenido_html' => $request->contenido_html,
            'Tipo_contenido' => $request->tipo_contenido ?? 'texto',
            'Url_video' => $request->url_video,
            'Archivo_adjunto' => $nombreArchivo,
            'Orden' => $request->orden
        ]);

        Log::info("Tema creado exitosamente para módulo: $idModulo - Nombre: {$request->nombre}");

        return back()->with('success', 'Tema creado exitosamente.');

    } catch (\Exception $e) {
        Log::error('ERROR al crear tema: ' . $e->getMessage());
        Log::error('Trace: ' . $e->getTraceAsString());
        return back()->with('error', 'Error al crear el tema: ' . $e->getMessage());
    }
}
/**
 * ELIMINAR TEMA
 */
public function eliminarTema($idTema)
{
    if (!session('usuario')) {
        return response()->json(['success' => false, 'message' => 'No autenticado']);
    }

    try {
        $tema = Tema::find($idTema);
        
        if (!$tema) {
            return response()->json(['success' => false, 'message' => 'Tema no encontrado']);
        }

        // Verificar si el tema tiene progreso asociado
        $tieneProgreso = DB::table('progreso_tema')->where('Id_tema', $idTema)->exists();
        
        if ($tieneProgreso) {
            return response()->json([
                'success' => false, 
                'message' => 'No se puede eliminar el tema porque tiene progreso de estudiantes asociado.'
            ]);
        }

        $idModulo = $tema->Id_modulo;
        $tema->delete();

        return response()->json([
            'success' => true, 
            'message' => 'Tema eliminado exitosamente.'
        ]);

    } catch (\Exception $e) {
        Log::error('ERROR al eliminar tema: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Error al eliminar el tema.']);
    }
}
// En DocenteController.php - Agrega este método

public function verDetalleEstudiante($idCurso, $idEstudiante)
{
    if (!session('usuario')) {
        return redirect('/login');
    }

    try {
        // Obtener información del curso
        $curso = DB::table('cursos')->where('Id_curso', $idCurso)->first();
        
        if (!$curso) {
            return back()->with('error', 'Curso no encontrado.');
        }

        // Obtener información del estudiante
        $estudiante = DB::table('usuarios')
            ->where('Id_usuario', $idEstudiante)
            ->first();

        if (!$estudiante) {
            return back()->with('error', 'Estudiante no encontrado.');
        }

        // Verificar que el estudiante esté inscrito en el curso
        $inscripcion = DB::table('inscripciones')
            ->where('Id_curso', $idCurso)
            ->where('Id_usuario', $idEstudiante)
            ->where('Estado', 1)
            ->first();

        if (!$inscripcion) {
            return back()->with('error', 'El estudiante no está inscrito en este curso.');
        }

        // Obtener progreso del estudiante en el curso (DATOS REALES)
        $progreso = DB::table('progreso_curso')
            ->where('Id_curso', $idCurso)
            ->where('Id_usuario', $idEstudiante)
            ->first();

        // Obtener temas completados por el estudiante (DATOS REALES)
        $temasCompletados = DB::select("
            SELECT 
                t.Id_tema,
                t.Nombre as tema_nombre,
                t.Descripcion as tema_descripcion,
                m.Nombre as modulo_nombre,
                pt.Fecha_completado,
                pt.Porcentaje,
                pt.Completado
            FROM progreso_tema pt
            INNER JOIN temas t ON pt.Id_tema = t.Id_tema
            INNER JOIN modulos m ON t.Id_modulo = m.Id_modulo
            WHERE pt.Id_usuario = ? 
            AND m.Id_curso = ?
            AND pt.Completado = 1
            ORDER BY m.Id_modulo, t.Orden
        ", [$idEstudiante, $idCurso]);

        // Obtener evaluaciones completadas (DATOS REALES)
        $evaluacionesCompletadas = DB::select("
            SELECT 
                e.Id_evaluacion,
                e.Tipo,
                e.Puntaje_maximo,
                pe.Puntaje_obtenido,
                pe.Porcentaje,
                pe.Aprobado,
                pe.Fecha_completado,
                m.Nombre as modulo_nombre
            FROM progreso_evaluacion pe
            INNER JOIN evaluaciones e ON pe.Id_evaluacion = e.Id_evaluacion
            INNER JOIN modulos m ON e.Id_modulo = m.Id_modulo
            WHERE pe.Id_usuario = ? 
            AND m.Id_curso = ?
            ORDER BY pe.Fecha_completado DESC
        ", [$idEstudiante, $idCurso]);

        // Obtener entregas del estudiante (DATOS REALES)
        $entregas = DB::select("
            SELECT 
                ent.Id_entrega,
                ent.Descripcion,
                ent.Archivo,
                ent.Fecha_entrega,
                ent.Puntos_asignados,
                ent.Estado,
                ent.Comentario_docente,
                e.Tipo as evaluacion_tipo,
                m.Nombre as modulo_nombre
            FROM entregas ent
            INNER JOIN evaluaciones e ON ent.Id_evaluacion = e.Id_evaluacion
            INNER JOIN modulos m ON e.Id_modulo = m.Id_modulo
            WHERE ent.Id_usuario = ? 
            AND m.Id_curso = ?
            ORDER BY ent.Fecha_entrega DESC
        ", [$idEstudiante, $idCurso]);

        // Obtener puntos del estudiante (DATOS REALES)
        $puntos = DB::table('gestion_puntos')
            ->where('Id_usuario', $idEstudiante)
            ->first();

        // Obtener ranking del estudiante (DATOS REALES)
        $ranking = DB::table('ranking')
            ->where('Id_usuario', $idEstudiante)
            ->where('Periodo', date('Y-m'))
            ->first();

        // Calcular estadísticas reales
        $totalTemasCurso = DB::table('temas as t')
            ->join('modulos as m', 't.Id_modulo', '=', 'm.Id_modulo')
            ->where('m.Id_curso', $idCurso)
            ->count();

        $totalEvaluacionesCurso = DB::table('evaluaciones as e')
            ->join('modulos as m', 'e.Id_modulo', '=', 'm.Id_modulo')
            ->where('m.Id_curso', $idCurso)
            ->count();

        $evaluacionesAprobadas = collect($evaluacionesCompletadas)
            ->where('Aprobado', true)
            ->count();

        return view('docente.estudiante_detalle', [
            'usuario' => session('usuario'),
            'curso' => $curso,
            'estudiante' => $estudiante,
            'progreso' => $progreso,
            'temasCompletados' => collect($temasCompletados),
            'evaluacionesCompletadas' => collect($evaluacionesCompletadas),
            'entregas' => collect($entregas),
            'puntos' => $puntos,
            'ranking' => $ranking,
            'totalTemasCurso' => $totalTemasCurso,
            'totalEvaluacionesCurso' => $totalEvaluacionesCurso,
            'evaluacionesAprobadas' => $evaluacionesAprobadas
        ]);

    } catch (\Exception $e) {
        Log::error('Error al cargar detalle estudiante: ' . $e->getMessage());
        return back()->with('error', 'Error al cargar el detalle del estudiante: ' . $e->getMessage());
    }
}
/**
 * VER ENTREGAS PENDIENTES DE CALIFICACIÓN
 */
public function entregasPendientes()
{
    if (!session('usuario')) {
        return redirect('/login');
    }

    $usuario = session('usuario');

    try {
        // Obtener entregas pendientes de los cursos del docente
        $entregasPendientes = DB::select("
            SELECT 
                e.Id_entrega,
                e.Archivo,
                e.Descripcion,
                e.Fecha_entrega,
                ev.Tipo as evaluacion_tipo,
                ev.Puntaje_maximo,
                m.Nombre as modulo_nombre,
                c.Titulo as curso_titulo,
                u.Id_usuario,
                u.Nombre as estudiante_nombre,
                u.Apellido as estudiante_apellido,
                u.Email as estudiante_email
            FROM entregas e
            INNER JOIN evaluaciones ev ON e.Id_evaluacion = ev.Id_evaluacion
            INNER JOIN modulos m ON ev.Id_modulo = m.Id_modulo
            INNER JOIN cursos c ON m.Id_curso = c.Id_curso
            INNER JOIN usuarios u ON e.Id_usuario = u.Id_usuario
            WHERE e.Estado = 'pendiente'
            ORDER BY e.Fecha_entrega ASC
        ");

        return view('docente.entregas_pendientes', [
            'usuario' => $usuario,
            'entregasPendientes' => $entregasPendientes,
            'totalPendientes' => count($entregasPendientes)
        ]);

    } catch (\Exception $e) {
        Log::error('ERROR en entregasPendientes: ' . $e->getMessage());
        return redirect('/docente/dashboard')->with('error', 'Error al cargar las entregas pendientes.');
    }
}
/**
 * CALIFICAR ENTREGA Y ASIGNAR PUNTOS
 */
/**
 * CALIFICAR ENTREGA Y ASIGNAR PUNTOS - MEJORADO
 */
public function calificarEntrega(Request $request, $idEntrega)
{
    if (!session('usuario')) {
        return redirect('/login');
    }

    try {
        $request->validate([
            'puntos' => 'required|integer|min:0',
            'comentario' => 'nullable|string|max:500'
        ]);

        // Obtener información de la entrega
        $entrega = DB::selectOne("
            SELECT 
                e.*,
                ev.Puntaje_maximo,
                ev.Id_evaluacion,
                u.Id_usuario as estudiante_id
            FROM entregas e
            INNER JOIN evaluaciones ev ON e.Id_evaluacion = ev.Id_evaluacion
            INNER JOIN usuarios u ON e.Id_usuario = u.Id_usuario
            WHERE e.Id_entrega = ?
        ", [$idEntrega]);

        if (!$entrega) {
            return back()->with('error', 'Entrega no encontrada.');
        }

        // Validar que los puntos no excedan el máximo
        $puntosAsignados = min($request->puntos, $entrega->Puntaje_maximo);

        DB::beginTransaction();

        // 1. Actualizar entrega
        DB::table('entregas')
            ->where('Id_entrega', $idEntrega)
            ->update([
                'Puntos_asignados' => $puntosAsignados,
                'Comentario_docente' => $request->comentario,
                'Estado' => 'calificado',
                'Fecha_calificacion' => now()
            ]);

        // 2. Crear o actualizar progreso_evaluacion
        DB::table('progreso_evaluacion')->updateOrInsert(
            [
                'Id_evaluacion' => $entrega->Id_evaluacion,
                'Id_usuario' => $entrega->estudiante_id
            ],
            [
                'Puntaje_obtenido' => $puntosAsignados,
                'Porcentaje' => ($puntosAsignados / $entrega->Puntaje_maximo) * 100,
                'Aprobado' => $puntosAsignados >= ($entrega->Puntaje_maximo * 0.6), // 60% para aprobar
                'Fecha_completado' => now()
            ]
        );

        // 3. ASIGNAR PUNTOS AL ESTUDIANTE EN gestion_puntos
        $this->asignarPuntosEstudiante($entrega->estudiante_id, $puntosAsignados, "Evaluación calificada - Entrega ID: $idEntrega");

        DB::commit();

        Log::info("✅ Entrega calificada - ID: $idEntrega, Puntos: $puntosAsignados, Estudiante: {$entrega->estudiante_id}");

        return redirect()->route('docente.curso.detalle', ['idCurso' => DB::table('evaluaciones as e')
            ->join('modulos as m', 'e.Id_modulo', '=', 'm.Id_modulo')
            ->where('e.Id_evaluacion', $entrega->Id_evaluacion)
            ->value('m.Id_curso')])
            ->with('success', "Trabajo calificado exitosamente. Se asignaron $puntosAsignados puntos al estudiante.");

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('❌ ERROR calificando entrega: ' . $e->getMessage());
        return back()->with('error', 'Error al calificar el trabajo: ' . $e->getMessage());
    }
}

/**
 * ASIGNAR PUNTOS AL ESTUDIANTE - ACTUALIZADO PARA gestion_puntos
 */
private function asignarPuntosEstudiante($idEstudiante, $puntos, $razon)
{
    try {
        Log::info("💰 DOCENTE asignando puntos - Estudiante: $idEstudiante, Puntos: $puntos, Razón: $razon");

        // Buscar gestión de puntos del estudiante
        $gestion_puntos = DB::table('gestion_puntos')
            ->where('Id_usuario', $idEstudiante)
            ->first();

        if ($gestion_puntos) {
            // Actualizar puntos existentes
            DB::table('gestion_puntos')
                ->where('Id_usuario', $idEstudiante)
                ->update([
                    'Total_puntos_actual' => $gestion_puntos->Total_puntos_actual + $puntos,
                    'Total_puntos_acumulados' => $gestion_puntos->Total_puntos_acumulados + $puntos,
                    'puntos_acumulados_mes' => ($gestion_puntos->puntos_acumulados_mes ?? 0) + $puntos,
                    'puntos_acumulados_total' => ($gestion_puntos->puntos_acumulados_total ?? 0) + $puntos
                ]);
        } else {
            // Crear nueva gestión de puntos
            DB::table('gestion_puntos')->insert([
                'Id_usuario' => $idEstudiante,
                'Total_puntos_actual' => $puntos,
                'Total_puntos_acumulados' => $puntos,
                'puntos_acumulados_mes' => $puntos,
                'puntos_acumulados_total' => $puntos,
                'Total_saldo_usado' => 0,
                'puntos_canjeados' => 0,
                'Id_ranking' => null
            ]);
        }

        // Actualizar ranking del estudiante
        $this->actualizarRankingEstudiante($idEstudiante);

        Log::info("✅ Puntos asignados al estudiante $idEstudiante: +$puntos puntos");

    } catch (\Exception $e) {
        Log::error('❌ ERROR asignando puntos desde docente: ' . $e->getMessage());
    }
}
/**
 * ACTUALIZAR RANKING DEL ESTUDIANTE
 */
private function actualizarRankingEstudiante($idEstudiante)
{
    try {
        $gestion_puntos = DB::table('gestion_puntos')
            ->where('Id_usuario', $idEstudiante)
            ->first();

        if (!$gestion_puntos) return;

        $puntosTotales = $gestion_puntos->Total_puntos_acumulados;
        $periodo = date('Y-m');

        // Actualizar o crear ranking
        $ranking_existente = DB::table('ranking')
            ->where('Id_usuario', $idEstudiante)
            ->where('Periodo', $periodo)
            ->first();

        if ($ranking_existente) {
            DB::table('ranking')
                ->where('Id_ranking', $ranking_existente->Id_ranking)
                ->update(['Total_puntos_acumulados' => $puntosTotales]);
        } else {
            DB::table('ranking')->insert([
                'Id_usuario' => $idEstudiante,
                'Periodo' => $periodo,
                'Total_puntos_acumulados' => $puntosTotales,
                'Posicion' => 0
            ]);
        }

        // Recalcular posiciones
        $this->recalcularPosicionesRanking($periodo);

    } catch (\Exception $e) {
        Log::error('ERROR actualizando ranking estudiante: ' . $e->getMessage());
    }
}
private function recalcularPosicionesRanking($periodo)
{
    try {
        $rankings = DB::table('ranking')
            ->where('Periodo', $periodo)
            ->orderByDesc('Total_puntos_acumulados')
            ->get();

        $posicion = 1;
        foreach ($rankings as $ranking) {
            DB::table('ranking')
                ->where('Id_ranking', $ranking->Id_ranking)
                ->update(['Posicion' => $posicion]);
            $posicion++;
        }

        Log::info("✅ Posiciones de ranking recalculadas para el período: $periodo");

    } catch (\Exception $e) {
        Log::error('❌ ERROR recalculando posiciones de ranking: ' . $e->getMessage());
    }
}
/**
 * MOSTRAR FORMULARIO PARA CALIFICAR ENTREGA - CORREGIDO
 */
public function mostrarCalificarEntrega($idEntrega)
{
    if (!session('usuario')) {
        return redirect('/login');
    }

    try {
        Log::info("🔍 Cargando entrega para calificar - ID: $idEntrega");

        // Obtener información completa de la entrega
        $entrega = DB::selectOne("
            SELECT 
                e.*,
                u.Nombre as estudiante_nombre,
                u.Apellido as estudiante_apellido,
                u.Email as estudiante_email,
                ev.Tipo as evaluacion_tipo,
                ev.Puntaje_maximo,
                ev.Id_evaluacion,
                m.Nombre as modulo_nombre,
                c.Titulo as curso_titulo,
                c.Id_curso
            FROM entregas e
            INNER JOIN usuarios u ON e.Id_usuario = u.Id_usuario
            INNER JOIN evaluaciones ev ON e.Id_evaluacion = ev.Id_evaluacion
            INNER JOIN modulos m ON ev.Id_modulo = m.Id_modulo
            INNER JOIN cursos c ON m.Id_curso = c.Id_curso
            WHERE e.Id_entrega = ?
        ", [$idEntrega]);

        if (!$entrega) {
            Log::error("❌ Entrega no encontrada - ID: $idEntrega");
            return back()->with('error', 'Entrega no encontrada.');
        }

        Log::info("✅ Entrega encontrada - Estudiante: {$entrega->estudiante_nombre}, Evaluación: {$entrega->evaluacion_tipo}");

        return view('docente.calificar_entrega', [
            'usuario' => session('usuario'),
            'entrega' => $entrega
        ]);

    } catch (\Exception $e) {
        Log::error('❌ ERROR mostrando formulario de calificación: ' . $e->getMessage());
        Log::error('Trace: ' . $e->getTraceAsString());
        return back()->with('error', 'Error al cargar la entrega para calificar: ' . $e->getMessage());
    }
}
}