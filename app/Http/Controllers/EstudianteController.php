<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Curso;
class EstudianteController extends Controller
{
    public function dashboard()
{
    if (!session('usuario')) {
        return redirect('/login')->with('error', 'Debes iniciar sesión primero.');
    }

    $usuario = session('usuario');

    try {
        // CONSULTA DIRECTA - SIN JOIN COMPLEJOS
        $cursos_inscritos = DB::select("
            SELECT DISTINCT
                c.Id_curso,
                c.Titulo,
                c.Descripcion, 
                c.Duracion,
                c.Costo,
                0 as Porcentaje,
                1 as Nivel,
                0 as Modulos_completados,
                0 as Temas_completados,
                0 as Evaluaciones_superadas,
                0 as Actividades_superadas,
                (SELECT COUNT(*) FROM modulos m WHERE m.Id_curso = c.Id_curso) as total_modulos,
                (SELECT COUNT(*) FROM evaluaciones e 
                 INNER JOIN modulos m ON e.Id_modulo = m.Id_modulo 
                 WHERE m.Id_curso = c.Id_curso) as total_evaluaciones
            FROM inscripciones i
            INNER JOIN cursos c ON i.Id_curso = c.Id_curso
            WHERE i.Id_usuario = ?
            AND i.Estado = 1
            ORDER BY c.Titulo
        ", [$usuario->Id_usuario]);

        $cursos_inscritos = collect($cursos_inscritos);

        Log::info('Cursos inscritos FINAL: ' . $cursos_inscritos->count());

        // Resto del código igual...
        $cursos_disponibles = DB::table('cursos as c')
            ->whereNotExists(function ($query) use ($usuario) {
                $query->select(DB::raw(1))
                      ->from('inscripciones as i')
                      ->whereColumn('i.Id_curso', 'c.Id_curso')
                      ->where('i.Id_usuario', $usuario->Id_usuario)
                      ->where('i.Estado', 1);
            })
            ->get();

        $evaluaciones_pendientes = collect();
        $gestion_puntos = DB::table('gestion_puntos')
            ->where('Id_usuario', $usuario->Id_usuario)
            ->first();
        $ranking_actual = null;
        $insignias = collect();

        $progreso_general = [
            'total_cursos' => $cursos_inscritos->count(),
            'cursos_completados' => 0,
            'promedio_progreso' => 0,
            'total_evaluaciones' => 0,
            'total_actividades' => 0,
            'total_modulos' => $cursos_inscritos->sum('total_modulos') ?? 0,
            'total_evaluaciones_pendientes' => 0,
            'total_cursos_disponibles' => $cursos_disponibles->count()
        ];

        return view('estudiante.dashboard', compact(
            'usuario',
            'cursos_inscritos',
            'cursos_disponibles',
            'evaluaciones_pendientes',
            'gestion_puntos',
            'ranking_actual',
            'insignias',
            'progreso_general'
        ));

    } catch (\Exception $e) {
        Log::error('ERROR en dashboard: ' . $e->getMessage());
        
        return view('estudiante.dashboard', [
            'usuario' => $usuario,
            'cursos_inscritos' => collect(),
            'cursos_disponibles' => collect(),
            'evaluaciones_pendientes' => collect(),
            'gestion_puntos' => null,
            'ranking_actual' => null,
            'insignias' => collect(),
            'progreso_general' => [
                'total_cursos' => 0,
                'cursos_completados' => 0,
                'promedio_progreso' => 0,
                'total_evaluaciones' => 0,
                'total_actividades' => 0,
                'total_modulos' => 0,
                'total_evaluaciones_pendientes' => 0,
                'total_cursos_disponibles' => 0
            ]
        ]);
    }
}
    /**
     * PÁGINA DE EXPLORAR CURSOS (LISTA COMPLETA)
     */
    /**
 * EXPLORAR CURSOS - Página completa
 */


/**
 * EXPLORAR CURSOS - Versión corregida
 */
public function explorarCursos()
{
    if (!session('usuario')) {
        return redirect('/login');
    }

    $usuario = session('usuario');

    try {
        // Primero obtener cursos inscritos (misma lógica que dashboard)
        $cursos_inscritos_ids = DB::table('inscripciones as i')
            ->join('cursos as c', 'i.Id_curso', '=', 'c.Id_curso')
            ->select('c.Id_curso')
            ->where('i.Id_usuario', $usuario->Id_usuario)
            ->where('i.Estado', 1)
            ->pluck('c.Id_curso')
            ->toArray();

        // Luego obtener todos los cursos
        $todos_cursos = DB::table('cursos as c')
            ->select(
                'c.Id_curso',
                'c.Titulo',
                'c.Descripcion',
                'c.Duracion',
                'c.Costo',
                DB::raw('(SELECT COUNT(*) FROM modulos m WHERE m.Id_curso = c.Id_curso) as total_modulos'),
                DB::raw('(SELECT COUNT(*) FROM evaluaciones e INNER JOIN modulos m ON e.Id_modulo = m.Id_modulo WHERE m.Id_curso = c.Id_curso) as total_evaluaciones'),
                DB::raw('(SELECT COUNT(*) FROM inscripciones i WHERE i.Id_curso = c.Id_curso AND i.Estado = 1) as total_estudiantes'),
                DB::raw('CASE WHEN c.Id_curso IN (' . ($cursos_inscritos_ids ? implode(',', $cursos_inscritos_ids) : '0') . ') THEN 1 ELSE 0 END as ya_inscrito')
            )
            ->orderBy('c.Titulo')
            ->get();

        // Separar cursos - CORREGIDO: convertir a colección
        $cursos_disponibles = collect($todos_cursos)->where('ya_inscrito', 0)->values();
        $cursos_inscritos = collect($todos_cursos)->where('ya_inscrito', 1)->values();

        return view('estudiante.explorar_cursos', compact(
            'usuario',
            'cursos_disponibles',
            'cursos_inscritos',
            'todos_cursos'
        ));

    } catch (\Exception $e) {
        Log::error('ERROR en explorar cursos: ' . $e->getMessage());
        return redirect('/estudiante/dashboard')->with('error', 'Error al cargar los cursos: ' . $e->getMessage());
    }
}
/**
 * INSCRIPCIÓN A CURSO 
 */
public function inscribirseCurso(Request $request, $idCurso)
{
    if (!session('usuario')) {
        return redirect('/login');
    }

    $usuario = session('usuario');

    try {
        DB::transaction(function () use ($usuario, $idCurso) {
            // Verificar si el curso existe
            $curso = DB::table('cursos')
                ->where('Id_curso', $idCurso)
                ->first();

            if (!$curso) {
                throw new \Exception('El curso no existe.');
            }

            // Verificar si ya está inscrito
            $inscripcionExistente = DB::table('inscripciones')
                ->where('Id_usuario', $usuario->Id_usuario)
                ->where('Id_curso', $idCurso)
                ->where('Estado', 1)
                ->exists();

            if ($inscripcionExistente) {
                throw new \Exception('Ya estás inscrito en este curso.');
            }

            // Crear inscripción
            DB::table('inscripciones')->insert([
                'Id_usuario' => $usuario->Id_usuario,
                'Id_curso' => $idCurso,
                'Fecha_inscripcion' => now()->format('Y-m-d'),
                'Estado' => 1
            ]);

            // Crear progreso inicial - CORREGIDO (Nivel como número)
            DB::table('progreso_curso')->insert([
                'Id_Usuario' => $usuario->Id_usuario,
                'Id_curso' => $idCurso,
                'Fecha_actualizacion' => now()->format('Y-m-d H:i:s'),
                'Porcentaje' => 0,
                'Nivel' => 1, // Cambiado de 'Principiante' a 1
                'Modulos_completados' => 0,
                'Temas_completados' => 0,
                'Evaluaciones_superadas' => 0,
                'Actividades_superadas' => 0
            ]);

            Log::info('INSCRIPCIÓN EXITOSA - Usuario: ' . $usuario->Id_usuario . ', Curso: ' . $idCurso);
        });

        return redirect()->route('estudiante.explorar_cursos')->with('success', '¡Te has inscrito al curso exitosamente!');

    } catch (\Exception $e) {
        Log::error('ERROR en inscripción: ' . $e->getMessage());
        return redirect()->route('estudiante.explorar_cursos')->with('error', $e->getMessage());
    }
}

    /**
     * INSCRIPCIÓN AUTOMÁTICA
     */
  

    /**
     * VER DETALLES DEL CURSO
     */
    
    /**
 * VER CURSO COMPLETO CON NAVEGACIÓN - VERSIÓN CORREGIDA CON ESTRUCTURA REAL
 */
/**
 * VER CURSO COMPLETO - VERSIÓN CORREGIDA (EVALUACIONES)
 */
public function verCurso($idCurso)
{
    if (!session('usuario')) {
        return redirect('/login');
    }

    $usuario = session('usuario');

    try {
        // Verificar que el usuario está inscrito
        $inscripcion = DB::table('inscripciones')
            ->where('Id_usuario', $usuario->Id_usuario)
            ->where('Id_curso', $idCurso)
            ->where('Estado', 1)
            ->first();

        if (!$inscripcion) {
            return redirect('/estudiante/dashboard')->with('error', 'No estás inscrito en este curso.');
        }

        // Obtener información del curso
        $curso = DB::table('cursos')->where('Id_curso', $idCurso)->first();
        
        // Obtener o crear progreso del curso
        $progreso = DB::table('progreso_curso')
            ->where('Id_usuario', $usuario->Id_usuario)
            ->where('Id_curso', $idCurso)
            ->first();

        if (!$progreso) {
            // Crear progreso si no existe
            $progreso_id = DB::table('progreso_curso')->insertGetId([
                'Id_usuario' => $usuario->Id_usuario,
                'Id_curso' => $idCurso,
                'Fecha_actualizacion' => now(),
                'Porcentaje' => 0,
                'Nivel' => 1,
                'Modulos_completados' => 0,
                'Temas_completados' => 0,
                'Evaluaciones_superadas' => 0,
                'Actividades_superadas' => 0
            ]);
            $progreso = DB::table('progreso_curso')->where('Id_progreso', $progreso_id)->first();
        }

        // Obtener módulos con sus temas
        $modulos = DB::select("
            SELECT 
                m.*,
                (SELECT COUNT(*) FROM temas t WHERE t.Id_modulo = m.Id_modulo) as total_temas,
                (SELECT COUNT(*) FROM progreso_tema pt 
                 INNER JOIN temas t ON pt.Id_tema = t.Id_tema 
                 WHERE t.Id_modulo = m.Id_modulo 
                 AND pt.Completado = 1) as temas_completados
            FROM modulos m
            WHERE m.Id_curso = ?
            ORDER BY m.Id_modulo
        ", [$idCurso]);

        // Calcular total de temas del curso
        $total_temas_curso = DB::selectOne("
            SELECT COUNT(*) as total FROM temas t
            INNER JOIN modulos m ON t.Id_modulo = m.Id_modulo
            WHERE m.Id_curso = ?
        ", [$idCurso])->total;

        // Encontrar el próximo tema pendiente para "Continuar"
        $proximo_tema_pendiente = $this->obtenerProximoTemaPendiente($usuario->Id_usuario, $idCurso);

        // Obtener evaluaciones pendientes del curso - CONSULTA CORREGIDA
       // En el método verCurso, usa ESTA consulta para evaluaciones:

// Obtener evaluaciones pendientes - CONSULTA SIMPLIFICADA Y PROBADA
$evaluaciones_pendientes = DB::select("
    SELECT 
        e.Id_evaluacion,
        e.Tipo,
        e.Puntaje_maximo,
        e.Fecha_inicio,
        e.Fecha_fin,
        m.Nombre as modulo_nombre,
        m.Id_modulo
    FROM evaluaciones e
    INNER JOIN modulos m ON e.Id_modulo = m.Id_modulo
    WHERE m.Id_curso = ?
    AND NOT EXISTS (
        SELECT 1 
        FROM progreso_evaluacion pe 
        WHERE pe.Id_evaluacion = e.Id_evaluacion 
        AND pe.Aprobado = 1
    )
    ORDER BY e.Fecha_inicio ASC
", [$idCurso]);

Log::info("📋 Evaluaciones pendientes encontradas: " . count($evaluaciones_pendientes));

        return view('estudiante.curso_detalle', compact(
            'usuario',
            'curso',
            'progreso',
            'modulos',
            'proximo_tema_pendiente',
            'evaluaciones_pendientes',
            'total_temas_curso'
        ));

    } catch (\Exception $e) {
        Log::error('ERROR al ver curso: ' . $e->getMessage());
        Log::error('Trace: ' . $e->getTraceAsString());
        return redirect('/estudiante/dashboard')->with('error', 'Error al cargar el curso: ' . $e->getMessage());
    }
}

/**
 * OBTENER PRÓXIMO TEMA PENDIENTE - VERSIÓN SIMPLIFICADA
 */
private function obtenerProximoTemaPendiente($idUsuario, $idCurso)
{
    return DB::selectOne("
        SELECT t.*, m.Nombre as modulo_nombre, m.Id_modulo
        FROM temas t
        INNER JOIN modulos m ON t.Id_modulo = m.Id_modulo
        WHERE m.Id_curso = ?
        AND t.Id_tema NOT IN (
            SELECT pt.Id_tema 
            FROM progreso_tema pt 
            WHERE pt.Completado = 1
        )
        ORDER BY m.Id_modulo, t.Orden
        LIMIT 1
    ", [$idCurso]);
}

/**
 * COMPLETAR TEMA Y ASIGNAR PUNTOS - VERSIÓN CORREGIDA
 */
/**
 * COMPLETAR TEMA Y ASIGNAR PUNTOS - VERSIÓN CON MEJOR MANEJO DE ERRORES
 */
/**
 * COMPLETAR TEMA - VERSIÓN FINAL CORREGIDA
 */
/**
 * COMPLETAR TEMA - CON MÁS LOGS PARA DEBUG
 */
/**
 * COMPLETAR TEMA - VERSIÓN COMPLETAMENTE REVISADA
 */
public function completarTema(Request $request, $idCurso, $idTema)
{
    if (!session('usuario')) {
        Log::error("❌ No hay usuario en sesión");
        return response()->json(['success' => false, 'message' => 'No autenticado']);
    }

    $usuario = session('usuario');
    $usuarioId = $usuario->Id_usuario;

    Log::info("🎯 === INICIANDO COMPLETADO DE TEMA ===");
    Log::info("Usuario ID: $usuarioId, Curso: $idCurso, Tema: $idTema");

    try {
        DB::beginTransaction();

        // 1. VERIFICAR SI EL TEMA YA ESTÁ COMPLETADO
        $progreso_tema = DB::table('progreso_tema')
            ->where('Id_tema', $idTema)
            ->where('Completado', 1)
            ->first();

        if ($progreso_tema) {
            Log::warning("⚠️ Tema $idTema YA estaba completado");
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Este tema ya estaba completado']);
        }

        Log::info("✅ Tema $idTema no completado, procediendo...");

        // 2. CREAR REGISTRO EN progreso_tema
        DB::table('progreso_tema')->insert([
            'Id_tema' => $idTema,
            'Completado' => 1,
            'Porcentaje' => 100,
            'Fecha_completado' => now()
        ]);

        Log::info("✅ Progreso_tema creado para tema $idTema");

        // 3. OBTENER PROGRESO DEL CURSO
        $progreso = DB::table('progreso_curso')
            ->where('Id_usuario', $usuarioId)
            ->where('Id_curso', $idCurso)
            ->first();

        if (!$progreso) {
            Log::error("❌ No se encontró progreso del curso $idCurso para usuario $usuarioId");
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'No estás inscrito en este curso']);
        }

        Log::info("📊 Progreso del curso encontrado - Temas completados: {$progreso->Temas_completados}");

        // 4. ACTUALIZAR ESTADÍSTICAS DEL CURSO
        $nuevos_temas_completados = $progreso->Temas_completados + 1;
        
        // Calcular total de temas del curso
        $total_temas_curso = DB::selectOne("
            SELECT COUNT(*) as total 
            FROM temas t
            INNER JOIN modulos m ON t.Id_modulo = m.Id_modulo
            WHERE m.Id_curso = ?
        ", [$idCurso])->total;

        $nuevo_porcentaje = min(100, round(($nuevos_temas_completados / $total_temas_curso) * 100));

        Log::info("📈 Progreso actual: $nuevos_temas_completados/$total_temas_curso temas = $nuevo_porcentaje%");

        // Actualizar progreso del curso
        DB::table('progreso_curso')
            ->where('Id_progreso', $progreso->Id_progreso)
            ->update([
                'Temas_completados' => $nuevos_temas_completados,
                'Porcentaje' => $nuevo_porcentaje,
                'Fecha_actualizacion' => now()
            ]);

        Log::info("✅ Progreso del curso actualizado");

        // 5. ASIGNAR PUNTOS POR TEMA COMPLETADO (10 puntos)
        Log::info("💰 Asignando 10 puntos por tema completado");
        $puntosAsignados = $this->asignarPuntos($usuarioId, 10, "Tema completado - Curso: $idCurso, Tema: $idTema");

        // 6. VERIFICAR MÓDULO COMPLETADO
        $moduloCompletado = $this->verificarYAsignarPuntosModulo($usuarioId, $idCurso, $idTema);
        
        // 7. VERIFICAR CURSO COMPLETADO
        $cursoCompletado = $this->verificarYAsignarPuntosCurso($usuarioId, $idCurso);

        DB::commit();

        Log::info("🎉 === TEMA COMPLETADO EXITOSAMENTE ===");
        Log::info("📊 Resumen:");
        Log::info("   - Puntos por tema: +10");
        Log::info("   - Módulo completado: " . ($moduloCompletado ? 'SÍ (+50)' : 'NO'));
        Log::info("   - Curso completado: " . ($cursoCompletado ? 'SÍ (+200)' : 'NO'));

        return response()->json([
            'success' => true, 
            'message' => '¡Tema completado! +10 puntos' . 
                        ($moduloCompletado ? ' + Módulo completado! +50 puntos' : '') .
                        ($cursoCompletado ? ' + Curso completado! +200 puntos' : ''),
            'puntos_otorgados' => 10 + ($moduloCompletado ? 50 : 0) + ($cursoCompletado ? 200 : 0)
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('❌ ERROR en completarTema: ' . $e->getMessage());
        Log::error('Trace: ' . $e->getTraceAsString());
        return response()->json(['success' => false, 'message' => 'Error interno del sistema: ' . $e->getMessage()]);
    }
}
/**
 * VERIFICAR Y ASIGNAR PUNTOS POR MÓDULO COMPLETADO
 */
/**
 * VERIFICAR Y ASIGNAR PUNTOS POR MÓDULO COMPLETADO - RETORNA BOOLEAN
 */
private function verificarYAsignarPuntosModulo($idUsuario, $idCurso, $idTema)
{
    try {
        // Obtener el módulo del tema
        $tema = DB::table('temas')->where('Id_tema', $idTema)->first();
        if (!$tema) return false;

        $idModulo = $tema->Id_modulo;

        // Contar temas completados vs totales del módulo
        $estadisticas_modulo = DB::selectOne("
            SELECT 
                COUNT(*) as total_temas,
                SUM(CASE WHEN pt.Completado = 1 THEN 1 ELSE 0 END) as temas_completados
            FROM temas t
            LEFT JOIN progreso_tema pt ON t.Id_tema = pt.Id_tema 
            WHERE t.Id_modulo = ?
        ", [$idModulo]);

        Log::info("🔍 Verificando módulo $idModulo: {$estadisticas_modulo->temas_completados}/{$estadisticas_modulo->total_temas} temas completados");

        // Si todos los temas están completados, asignar puntos por módulo
        if ($estadisticas_modulo->temas_completados >= $estadisticas_modulo->total_temas) {
            Log::info("🎉 Módulo $idModulo completado! Asignando 50 puntos");
            
            $puntosAsignados = $this->asignarPuntos($idUsuario, 50, "Módulo completado - ID: $idModulo");
            
            if ($puntosAsignados) {
                // Actualizar módulos completados en progreso_curso
                $progreso = DB::table('progreso_curso')
                    ->where('Id_usuario', $idUsuario)
                    ->where('Id_curso', $idCurso)
                    ->first();

                if ($progreso) {
                    DB::table('progreso_curso')
                        ->where('Id_progreso', $progreso->Id_progreso)
                        ->update([
                            'Modulos_completados' => $progreso->Modulos_completados + 1
                        ]);
                    
                    Log::info("✅ Módulos completados actualizados: " . ($progreso->Modulos_completados + 1));
                }
                
                return true;
            }
        }

        return false;

    } catch (\Exception $e) {
        Log::error('ERROR en verificarYAsignarPuntosModulo: ' . $e->getMessage());
        return false;
    }
}
/**
 * INICIALIZAR RANKING COMPLETO - EJECUTAR UNA SOLA VEZ
 */
public function inicializarRankingCompleto()
{
    try {
        Log::info("🎯 INICIALIZANDO RANKING COMPLETO");
        
        $periodo = date('Y-m');
        
        // 1. Obtener todos los usuarios que tienen puntos
        $usuariosConPuntos = DB::select("
            SELECT gp.Id_usuario, gp.Total_puntos_acumulados, u.Nombre, u.Apellido
            FROM gestion_puntos gp
            INNER JOIN usuarios u ON gp.Id_usuario = u.Id_usuario
            ORDER BY gp.Total_puntos_acumulados DESC
        ");

        Log::info("👥 Usuarios con puntos encontrados: " . count($usuariosConPuntos));

        // 2. Limpiar ranking del periodo actual (opcional)
        DB::table('ranking')->where('Periodo', $periodo)->delete();

        // 3. Insertar todos los usuarios en el ranking
        $posicion = 1;
        foreach ($usuariosConPuntos as $usuario) {
            DB::table('ranking')->insert([
                'Id_usuario' => $usuario->Id_usuario,
                'Periodo' => $periodo,
                'Total_puntos_acumulados' => $usuario->Total_puntos_acumulados,
                'Posicion' => $posicion
            ]);
            
            Log::info("   $posicion. {$usuario->Nombre} {$usuario->Apellido} - {$usuario->Total_puntos_acumulados} puntos");
            $posicion++;
        }

        Log::info("✅ Ranking inicializado con $posicion usuarios");

        return response()->json([
            'success' => true,
            'message' => "Ranking inicializado con " . count($usuariosConPuntos) . " usuarios",
            'periodo' => $periodo
        ]);

    } catch (\Exception $e) {
        Log::error('❌ ERROR inicializando ranking: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}
/**
 * VER RANKING COMPLETO
 */
/**
 * MOSTRAR PÁGINA DE RANKING
 */
public function mostrarRanking()
{
    if (!session('usuario')) {
        return redirect('/login');
    }

    $usuario = session('usuario');

    try {
        $periodo = date('Y-m');
        
        // Obtener ranking completo
        $ranking = DB::select("
            SELECT 
                r.Posicion,
                r.Total_puntos_acumulados,
                u.Id_usuario,
                u.Nombre,
                u.Apellido,
                u.Email,
                -- Verificar si es el usuario actual
                CASE WHEN u.Id_usuario = ? THEN 1 ELSE 0 END as es_usuario_actual
            FROM ranking r
            INNER JOIN usuarios u ON r.Id_usuario = u.Id_usuario
            WHERE r.Periodo = ?
            ORDER BY r.Posicion ASC
            LIMIT 50
        ", [$usuario->Id_usuario, $periodo]);

        // Obtener posición del usuario actual
        $miPosicion = collect($ranking)->where('es_usuario_actual', 1)->first();

        return view('estudiante.ranking', [
            'usuario' => $usuario,
            'ranking' => $ranking,
            'miPosicion' => $miPosicion,
            'periodo' => $periodo,
            'total_participantes' => count($ranking)
        ]);

    } catch (\Exception $e) {
        Log::error('ERROR en mostrarRanking: ' . $e->getMessage());
        return redirect('/estudiante/dashboard')->with('error', 'Error al cargar el ranking');
    }
}
/**
 * VERIFICAR Y ASIGNAR PUNTOS POR CURSO COMPLETADO
 */
private function verificarYAsignarPuntosCurso($idUsuario, $idCurso): bool
{
    try {
        $progreso = DB::table('progreso_curso')
            ->where('Id_usuario', $idUsuario)
            ->where('Id_curso', $idCurso)
            ->first();

        if ($progreso && $progreso->Porcentaje >= 100) {
            Log::info("🎊 Curso $idCurso completado al 100%! Asignando 200 puntos");
            
            $asignado = $this->asignarPuntos($idUsuario, 200, "Curso completado - ID: $idCurso");
            
            // Actualizar nivel solo si se asignaron puntos correctamente
            if ($asignado) {
                DB::table('progreso_curso')
                    ->where('Id_progreso', $progreso->Id_progreso)
                    ->update([
                        'Nivel' => 2 // Subir de nivel
                    ]);
                
                Log::info("✅ Nivel actualizado a 2 para curso completado");
                return true;
            }

            Log::warning("⚠️ No se pudieron asignar puntos para curso $idCurso");
            return false;
        }

        return false;

    } catch (\Exception $e) {
        Log::error('ERROR en verificarYAsignarPuntosCurso: ' . $e->getMessage());
        return false;
    }
}
/**
 * VERIFICAR SI SE COMPLETÓ UN MÓDULO - VERSIÓN CORREGIDA
 */
private function verificarModuloCompletado($idUsuario, $idCurso, $idTema)
{
    // Obtener el módulo del tema
    $tema = DB::table('temas')->where('Id_tema', $idTema)->first();
    if (!$tema) return;

    $idModulo = $tema->Id_modulo;

    // Contar temas completados vs totales del módulo
    $estadisticas_modulo = DB::selectOne("
        SELECT 
            COUNT(*) as total_temas,
            SUM(CASE WHEN pt.Completado = 1 THEN 1 ELSE 0 END) as temas_completados
        FROM temas t
        LEFT JOIN progreso_tema pt ON t.Id_tema = pt.Id_tema 
        WHERE t.Id_modulo = ?
    ", [$idModulo]);

    // Si todos los temas están completados, asignar puntos por módulo
    if ($estadisticas_modulo->temas_completados >= $estadisticas_modulo->total_temas) {
        $this->asignarPuntos($idUsuario, 50, "Módulo completado - ID: $idModulo");
        
        // Actualizar módulos completados en progreso_curso
        $progreso = DB::table('progreso_curso')
            ->where('Id_usuario', $idUsuario)
            ->where('Id_curso', $idCurso)
            ->first();

        if ($progreso) {
            DB::table('progreso_curso')
                ->where('Id_progreso', $progreso->Id_progreso)
                ->update([
                    'Modulos_completados' => $progreso->Modulos_completados + 1
                ]);
        }

        // Verificar si se completó el curso completo
        $this->verificarCursoCompletado($idUsuario, $idCurso);
    }
}

/**
 * VERIFICAR SI SE COMPLETÓ EL CURSO - VERSIÓN CORREGIDA
 */
private function verificarCursoCompletado($idUsuario, $idCurso)
{
    $progreso = DB::table('progreso_curso')
        ->where('Id_usuario', $idUsuario)
        ->where('Id_curso', $idCurso)
        ->first();

    if ($progreso && $progreso->Porcentaje >= 100) {
        $this->asignarPuntos($idUsuario, 200, "Curso completado - ID: $idCurso");
        
        // Actualizar nivel
        DB::table('progreso_curso')
            ->where('Id_progreso', $progreso->Id_progreso)
            ->update([
                'Nivel' => 2 // Subir de nivel
            ]);
    }
}
/**
 * ASIGNAR PUNTOS AL USUARIO - VERSIÓN CORREGIDA CON ESTRUCTURA REAL
 */
/**
 * ASIGNAR PUNTOS AL USUARIO - VERSIÓN DEFINITIVAMENTE CORREGIDA
 */
/**
 * ASIGNAR PUNTOS AL USUARIO - VERSIÓN CORREGIDA CON ESTRUCTURA EXACTA
 */
/**
 * ASIGNAR PUNTOS AL USUARIO - VERSIÓN MEJORADA
 */
/**
 * ASIGNAR PUNTOS AL USUARIO - VERSIÓN MEJORADA CON CREACIÓN INICIAL
 */
/**
 * ASIGNAR PUNTOS AL USUARIO - VERSIÓN QUE RETORNA RESULTADO
 */
private function asignarPuntos($idUsuario, $puntos, $razon)
{
    try {
        Log::info("💰 ASIGNANDO PUNTOS: Usuario $idUsuario, Puntos: $puntos, Razón: $razon");

        // Buscar gestión de puntos existente
        $gestion_puntos = DB::table('gestion_puntos')
            ->where('Id_usuario', $idUsuario)
            ->first();

        if ($gestion_puntos) {
            Log::info("📊 Puntos actuales - Actual: {$gestion_puntos->Total_puntos_actual}, Acumulados: {$gestion_puntos->Total_puntos_acumulados}");
            
            // Actualizar puntos existentes
            DB::table('gestion_puntos')
                ->where('Id_usuario', $idUsuario)
                ->update([
                    'Total_puntos_actual' => $gestion_puntos->Total_puntos_actual + $puntos,
                    'Total_puntos_acumulados' => $gestion_puntos->Total_puntos_acumulados + $puntos,
                    'puntos_acumulados_mes' => ($gestion_puntos->puntos_acumulados_mes ?? 0) + $puntos,
                    'puntos_acumulados_total' => ($gestion_puntos->puntos_acumulados_total ?? 0) + $puntos
                ]);

            Log::info("✅ Puntos actualizados: +$puntos puntos");

        } else {
            Log::info("🆕 Creando NUEVO registro de puntos para usuario $idUsuario");
            
            // Crear nueva gestión de puntos
            DB::table('gestion_puntos')->insert([
                'Id_usuario' => $idUsuario,
                'Total_puntos_actual' => $puntos,
                'Total_puntos_acumulados' => $puntos,
                'puntos_acumulados_mes' => $puntos,
                'puntos_acumulados_total' => $puntos,
                'Total_saldo_usado' => 0,
                'puntos_canjeados' => 0,
                'Id_ranking' => null
            ]);

            Log::info("✅ Nuevo registro de puntos creado con $puntos puntos");
        }

        // ACTUALIZAR RANKING
        $this->actualizarRanking($idUsuario);

        // VERIFICAR que se creó/actualizó correctamente
        $gestion_verificada = DB::table('gestion_puntos')
            ->where('Id_usuario', $idUsuario)
            ->first();
            
        if ($gestion_verificada) {
            Log::info("🔍 VERIFICACIÓN - Puntos actuales: {$gestion_verificada->Total_puntos_actual}");
            return true;
        } else {
            Log::error("❌ VERIFICACIÓN FALLIDA - No se pudo crear/actualizar puntos");
            return false;
        }

    } catch (\Exception $e) {
        Log::error('❌ ERROR en asignarPuntos: ' . $e->getMessage());
        return false;
    }
}
/**
 * ACTUALIZAR RANKING DEL USUARIO
 */
/**
 * ACTUALIZAR RANKING DEL USUARIO - VERSIÓN CORREGIDA
 */

/**
 * ACTUALIZAR PUNTOS DE TODOS LOS USUARIOS - EJECUTAR UNA VEZ
 */
public function actualizarPuntosGlobal()
{
    try {
        Log::info("🎯 ACTUALIZANDO PUNTOS DE TODOS LOS USUARIOS");
        
        // Obtener todos los usuarios
        $usuarios = DB::table('usuarios')->get();
        
        $totalActualizados = 0;
        
        foreach ($usuarios as $usuario) {
            // Calcular puntos basados en su progreso real
            $puntos = $this->calcularPuntosReales($usuario->Id_usuario);
            
            if ($puntos > 0) {
                // Actualizar o crear gestión de puntos
                $gestion_puntos = DB::table('gestion_puntos')
                    ->where('Id_usuario', $usuario->Id_usuario)
                    ->first();
                    
                if ($gestion_puntos) {
                    DB::table('gestion_puntos')
                        ->where('Id_usuario', $usuario->Id_usuario)
                        ->update([
                            'Total_puntos_actual' => $puntos,
                            'Total_puntos_acumulados' => $puntos
                        ]);
                } else {
                    DB::table('gestion_puntos')->insert([
                        'Id_usuario' => $usuario->Id_usuario,
                        'Total_puntos_actual' => $puntos,
                        'Total_puntos_acumulados' => $puntos,
                        'puntos_acumulados_mes' => $puntos,
                        'puntos_acumulados_total' => $puntos,
                        'Total_saldo_usado' => 0,
                        'puntos_canjeados' => 0,
                        'Id_ranking' => null
                    ]);
                }
                
                // Actualizar ranking
                $this->actualizarRanking($usuario->Id_usuario);
                
                $totalActualizados++;
                Log::info("   ✅ Usuario {$usuario->Id_usuario}: {$usuario->Nombre} - $puntos puntos");
            }
        }
        
        Log::info("🎉 PUNTOS ACTUALIZADOS PARA $totalActualizados USUARIOS");
        
        return response()->json([
            'success' => true,
            'message' => "Puntos actualizados para $totalActualizados usuarios",
            'total_actualizados' => $totalActualizados
        ]);
        
    } catch (\Exception $e) {
        Log::error('❌ ERROR actualizando puntos global: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}

/**
 * CALCULAR PUNTOS REALES DEL USUARIO
 */
private function calcularPuntosReales($idUsuario)
{
    // Puntos por temas completados
    $temasCompletados = DB::selectOne("
        SELECT COUNT(*) as total 
        FROM progreso_tema 
        WHERE Completado = 1
    ")->total;

    // Puntos por módulos completados
    $modulosCompletados = DB::selectOne("
        SELECT SUM(Modulos_completados) as total 
        FROM progreso_curso 
        WHERE Id_usuario = ?
    ", [$idUsuario])->total ?? 0;

    // Puntos por cursos completados
    $cursosCompletados = DB::selectOne("
        SELECT COUNT(*) as total 
        FROM progreso_curso 
        WHERE Id_usuario = ? AND Porcentaje >= 100
    ", [$idUsuario])->total;

    $puntosTotales = ($temasCompletados * 10) + ($modulosCompletados * 50) + ($cursosCompletados * 200);
    
    return $puntosTotales;
}
/**
 * RECALCULAR POSICIONES DEL RANKING
 */
private function recalcularPosicionesRanking($periodo)
{
    try {
        // Obtener todos los usuarios del periodo ordenados por puntos
        $rankings = DB::select("
            SELECT r.Id_ranking, r.Total_puntos_acumulados
            FROM ranking r
            WHERE r.Periodo = ?
            ORDER BY r.Total_puntos_acumulados DESC
        ", [$periodo]);

        // Actualizar posiciones
        $posicion = 1;
        foreach ($rankings as $ranking) {
            DB::table('ranking')
                ->where('Id_ranking', $ranking->Id_ranking)
                ->update(['Posicion' => $posicion]);
            $posicion++;
        }

        Log::info("🔄 Posiciones de ranking recalculadas para periodo $periodo");

    } catch (\Exception $e) {
        Log::error('ERROR en recalcularPosicionesRanking: ' . $e->getMessage());
    }
}
public function obtenerPuntosActuales()
{
    if (!session('usuario')) {
        return response()->json(['puntos_actual' => 0, 'puntos_acumulados' => 0]);
    }

    $usuario = session('usuario');
    
    try {
        $gestion_puntos = DB::table('gestion_puntos')
            ->where('Id_usuario', $usuario->Id_usuario)
            ->first();

        return response()->json([
            'puntos_actual' => $gestion_puntos ? $gestion_puntos->Total_puntos_actual : 0,
            'puntos_acumulados' => $gestion_puntos ? $gestion_puntos->Total_puntos_acumulados : 0
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'puntos_actual' => 0,
            'puntos_acumulados' => 0
        ]);
    }
}
/**
 * MÉTODO TEMPORAL PARA DEBUG DE PUNTOS - ELIMINAR DESPUÉS
 */
public function debugPuntos($idUsuario = null)
{
    if (!$idUsuario) {
        $idUsuario = session('usuario')->Id_usuario ?? 1;
    }

    try {
        // Verificar gestión de puntos
        $gestion_puntos = DB::table('gestion_puntos')
            ->where('Id_usuario', $idUsuario)
            ->first();

        // Verificar progreso de temas
        $temas_completados = DB::table('progreso_tema')
            ->where('Completado', 1)
            ->count();

        // Verificar progreso de cursos
        $progreso_cursos = DB::table('progreso_curso')
            ->where('Id_usuario', $idUsuario)
            ->get();

        return response()->json([
            'usuario_id' => $idUsuario,
            'gestion_puntos' => $gestion_puntos,
            'temas_completados_total' => $temas_completados,
            'progreso_cursos' => $progreso_cursos,
            'timestamp' => now()
        ]);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
}
/**
 * ARREGLAR TODO EL SISTEMA - EJECUTAR UNA VEZ
 */
public function fixTodo()
{
    try {
        Log::info("🚀 INICIANDO REPARACIÓN COMPLETA DEL SISTEMA");
        
        // 1. Actualizar puntos de todos los usuarios
        $this->actualizarPuntosGlobal();
        
        // 2. Inicializar ranking completo
        $this->inicializarRankingCompleto();
        
        Log::info("🎉 REPARACIÓN COMPLETADA EXITOSAMENTE");
        
        return response()->json([
            'success' => true,
            'message' => 'Sistema reparado completamente',
            'acciones' => [
                'Puntos actualizados para todos los usuarios',
                'Ranking inicializado correctamente',
                'Sistema listo para usar'
            ]
        ]);
        
    } catch (\Exception $e) {
        Log::error('❌ ERROR en reparación completa: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}
/**
 * ACTUALIZAR PUNTOS AUTOMÁTICAMENTE - MÉTODO DEFINITIVO
 */
public function actualizarPuntosAutomatico($idUsuario = null)
{
    if (!$idUsuario) {
        if (!session('usuario')) {
            return response()->json(['success' => false, 'message' => 'No autenticado']);
        }
        $idUsuario = session('usuario')->Id_usuario;
    }

    try {
        Log::info("🎯 ACTUALIZACIÓN AUTOMÁTICA DE PUNTOS PARA USUARIO: $idUsuario");

        // 1. CALCULAR PUNTOS REALES BASADOS EN PROGRESO
        $puntosCalculados = $this->calcularPuntosReales($idUsuario);
        
        Log::info("💰 Puntos calculados: $puntosCalculados");

        // 2. ACTUALIZAR GESTIÓN DE PUNTOS
        $gestion_puntos = DB::table('gestion_puntos')
            ->where('Id_usuario', $idUsuario)
            ->first();

        if ($gestion_puntos) {
            // Si los puntos calculados son MAYORES que los actuales, actualizar
            if ($puntosCalculados > $gestion_puntos->Total_puntos_acumulados) {
                DB::table('gestion_puntos')
                    ->where('Id_usuario', $idUsuario)
                    ->update([
                        'Total_puntos_actual' => $puntosCalculados,
                        'Total_puntos_acumulados' => $puntosCalculados,
                        'puntos_acumulados_mes' => $puntosCalculados,
                        'puntos_acumulados_total' => $puntosCalculados,
                        'updated_at' => now()
                    ]);
                Log::info("✅ Puntos actualizados: $puntosCalculados");
            } else {
                Log::info("ℹ️ Puntos ya están actualizados: {$gestion_puntos->Total_puntos_acumulados}");
            }
        } else {
            // Crear nuevo registro
            DB::table('gestion_puntos')->insert([
                'Id_usuario' => $idUsuario,
                'Total_puntos_actual' => $puntosCalculados,
                'Total_puntos_acumulados' => $puntosCalculados,
                'puntos_acumulados_mes' => $puntosCalculados,
                'puntos_acumulados_total' => $puntosCalculados,
                'Total_saldo_usado' => 0,
                'puntos_canjeados' => 0,
                'Id_ranking' => null,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            Log::info("✅ Nuevo registro creado: $puntosCalculados puntos");
        }

        // 3. ACTUALIZAR RANKING SIEMPRE
        $this->actualizarRankingForzado($idUsuario, $puntosCalculados);

        // 4. VERIFICAR RESULTADO
        $puntosFinales = DB::table('gestion_puntos')
            ->where('Id_usuario', $idUsuario)
            ->value('Total_puntos_actual');

        return response()->json([
            'success' => true,
            'message' => 'Puntos actualizados correctamente',
            'puntos_anteriores' => $gestion_puntos->Total_puntos_actual ?? 0,
            'puntos_nuevos' => $puntosCalculados,
            'puntos_finales' => $puntosFinales
        ]);

    } catch (\Exception $e) {
        Log::error('❌ ERROR en actualización automática: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}

/**
 * ACTUALIZAR RANKING FORZADO - SIEMPRE FUNCIONA
 */
private function actualizarRankingForzado($idUsuario, $puntosTotales)
{
    try {
        $periodo = date('Y-m');
        
        Log::info("📈 Actualizando ranking forzado - Usuario: $idUsuario, Puntos: $puntosTotales");

        // ELIMINAR registro existente del periodo actual
        DB::table('ranking')
            ->where('Id_usuario', $idUsuario)
            ->where('Periodo', $periodo)
            ->delete();

        // CREAR NUEVO registro
        $nuevoId = DB::table('ranking')->insertGetId([
            'Id_usuario' => $idUsuario,
            'Periodo' => $periodo,
            'Total_puntos_acumulados' => $puntosTotales,
            'Posicion' => 999 // Temporal, se recalcula después
        ]);

        Log::info("✅ Ranking creado: ID $nuevoId");

        // RECALCULAR TODAS LAS POSICIONES
        $this->recalcularTodasLasPosicionesRanking($periodo);

        // VERIFICAR POSICIÓN FINAL
        $posicionFinal = DB::table('ranking')
            ->where('Id_ranking', $nuevoId)
            ->value('Posicion');

        Log::info("🎯 Posición final: #$posicionFinal");

    } catch (\Exception $e) {
        Log::error('❌ ERROR en ranking forzado: ' . $e->getMessage());
    }
}
private function recalcularTodasLasPosicionesRanking($periodo)
{
    try {
        // Obtener todos los usuarios del periodo ordenados por puntos
        $rankings = DB::select("
            SELECT r.Id_ranking, r.Total_puntos_acumulados
            FROM ranking r
            WHERE r.Periodo = ?
            ORDER BY r.Total_puntos_acumulados DESC
        ", [$periodo]);

        // Actualizar posiciones
        $posicion = 1;
        foreach ($rankings as $ranking) {
            DB::table('ranking')
                ->where('Id_ranking', $ranking->Id_ranking)
                ->update(['Posicion' => $posicion]);
            $posicion++;
        }

        Log::info("🔄 Posiciones de ranking recalculadas para periodo $periodo");

    } catch (\Exception $e) {
        Log::error('ERROR en recalcularTodasLasPosicionesRanking: ' . $e->getMessage());
    }
}
/**
 * ACTUALIZAR PUNTOS DE TODOS LOS USUARIOS - VERSIÓN DEFINITIVA
 */
public function actualizarPuntosTodos()
{
    try {
        Log::info("🎯 ACTUALIZANDO PUNTOS DE TODOS LOS USUARIOS - VERSIÓN DEFINITIVA");
        
        // Obtener todos los usuarios (solo estudiantes)
        $usuarios = DB::select("
            SELECT u.Id_usuario, u.Nombre, u.Apellido
            FROM usuarios u
            INNER JOIN rol_usuario ru ON u.Id_usuario = ru.Id_usuario
            INNER JOIN roles r ON ru.Id_rol = r.Id_rol
            WHERE r.Nombre = 'Estudiante'
        ");
        
        $resultados = [];
        
        foreach ($usuarios as $usuario) {
            $resultado = $this->actualizarPuntosAutomatico($usuario->Id_usuario);
            $resultados[] = [
                'usuario' => $usuario->Nombre . ' ' . $usuario->Apellido,
                'id' => $usuario->Id_usuario,
                'success' => true
            ];
            
            // Pequeña pausa para no saturar
            usleep(100000); // 100ms
        }
        
        Log::info("🎉 ACTUALIZACIÓN MASIVA COMPLETADA: " . count($usuarios) . " usuarios");
        
        return response()->json([
            'success' => true,
            'message' => 'Puntos actualizados para ' . count($usuarios) . ' usuarios',
            'usuarios_actualizados' => count($usuarios),
            'detalles' => $resultados
        ]);
        
    } catch (\Exception $e) {
        Log::error('❌ ERROR en actualización masiva: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}
/**
 * COMENZAR EVALUACIÓN - Mostrar preguntas
 */
public function comenzarEvaluacion($idEvaluacion)
{
    if (!session('usuario')) {
        return redirect('/login');
    }

    try {
        $usuario = session('usuario');
        $idUsuario = $usuario->Id_usuario;

        // Obtener la evaluación
        $evaluacion = DB::table('evaluaciones as e')
            ->select('e.*', 'm.Nombre as modulo_nombre', 'c.Titulo as curso_titulo')
            ->join('modulos as m', 'e.Id_modulo', '=', 'm.Id_modulo')
            ->join('cursos as c', 'm.Id_curso', '=', 'c.Id_curso')
            ->where('e.Id_evaluacion', $idEvaluacion)
            ->first();

        if (!$evaluacion) {
            return back()->with('error', 'Evaluación no encontrada.');
        }

        // Verificar si el estudiante está inscrito en el curso
        $inscripcion = DB::table('inscripciones')
            ->where('Id_curso', function($query) use ($idEvaluacion) {
                $query->select('m.Id_curso')
                    ->from('evaluaciones as e')
                    ->join('modulos as m', 'e.Id_modulo', '=', 'm.Id_modulo')
                    ->where('e.Id_evaluacion', $idEvaluacion);
            })
            ->where('Id_usuario', $idUsuario)
            ->where('Estado', 1)
            ->first();

        if (!$inscripcion) {
            return back()->with('error', 'No estás inscrito en este curso.');
        }

        // Verificar si ya completó esta evaluación
        $progreso = DB::table('progreso_evaluacion')
            ->where('Id_evaluacion', $idEvaluacion)
            ->where('Id_usuario', $idUsuario)
            ->first();

        if ($progreso && $progreso->Aprobado) {
            return redirect()->route('estudiante.evaluacion.resultado', ['id' => $idEvaluacion])
                ->with('info', 'Ya completaste esta evaluación.');
        }

        return view('estudiante.evaluacion', [
            'usuario' => $usuario,
            'evaluacion' => $evaluacion,
            'progreso' => $progreso
        ]);

    } catch (\Exception $e) {
        Log::error('Error al cargar evaluación: ' . $e->getMessage());
        return back()->with('error', 'Error al cargar la evaluación.');
    }
}


/**
 * GENERAR PREGUNTAS DE EVALUACIÓN (simuladas)
 */
private function generarPreguntasEvaluacion($idEvaluacion)
{
    // Por ahora simulamos preguntas, luego puedes conectar con una tabla de preguntas
    return [
        [
            'id' => 1,
            'pregunta' => '¿Cuál es el concepto fundamental de la programación orientada a objetos?',
            'opciones' => [
                'A' => 'Herencia',
                'B' => 'Polimorfismo', 
                'C' => 'Encapsulamiento',
                'D' => 'Todas las anteriores'
            ],
            'respuesta_correcta' => 'D',
            'puntos' => 10
        ],
        [
            'id' => 2,
            'pregunta' => 'En bases de datos, ¿qué es una clave primaria?',
            'opciones' => [
                'A' => 'Un campo que identifica unívocamente cada registro',
                'B' => 'Un índice que mejora el rendimiento',
                'C' => 'Una restricción de integridad referencial',
                'D' => 'Un tipo de dato especial'
            ],
            'respuesta_correcta' => 'A',
            'puntos' => 10
        ],
        [
            'id' => 3, 
            'pregunta' => '¿Qué protocolo se utiliza principalmente para páginas web seguras (HTTPS)?',
            'opciones' => [
                'A' => 'FTP',
                'B' => 'SSL/TLS',
                'C' => 'HTTP',
                'D' => 'SMTP'
            ],
            'respuesta_correcta' => 'B',
            'puntos' => 10
        ]
    ];
}
/**
 * PROCESAR EVALUACIÓN Y ASIGNAR PUNTOS
 */
private function calcularPuntaje($respuestas, $evaluacion)
{
    // Aquí implementas tu lógica de corrección
    // Por ahora, devolvemos un puntaje simulado
    return rand(70, 100); // Puntaje aleatorio entre 70-100
}


/**
 * VER RESULTADO DE LA EVALUACIÓN
 */

/**
 * ACTUALIZAR PROGRESO DEL CURSO POR EVALUACIÓN
 */
private function actualizarProgresoCursoEvaluacion($idUsuario, $idEvaluacion, $aprobado)
{
    try {
        // Obtener el curso de la evaluación
        $evaluacionCurso = DB::selectOne("
            SELECT c.Id_curso, m.Id_modulo
            FROM evaluaciones e
            INNER JOIN modulos m ON e.Id_modulo = m.Id_modulo
            INNER JOIN cursos c ON m.Id_curso = c.Id_curso
            WHERE e.Id_evaluacion = ?
        ", [$idEvaluacion]);

        if (!$evaluacionCurso) return;

        $idCurso = $evaluacionCurso->Id_curso;

        // Obtener progreso actual del curso
        $progreso = DB::table('progreso_curso')
            ->where('Id_usuario', $idUsuario)
            ->where('Id_curso', $idCurso)
            ->first();

        if ($progreso && $aprobado) {
            // Incrementar evaluaciones superadas
            $nuevasEvaluacionesSuperadas = $progreso->Evaluaciones_superadas + 1;
            
            DB::table('progreso_curso')
                ->where('Id_progreso', $progreso->Id_progreso)
                ->update([
                    'Evaluaciones_superadas' => $nuevasEvaluacionesSuperadas,
                    'Fecha_actualizacion' => now()
                ]);

            Log::info("✅ Evaluaciones superadas actualizadas: $nuevasEvaluacionesSuperadas");
        }

    } catch (\Exception $e) {
        Log::error('ERROR actualizando progreso curso: ' . $e->getMessage());
    }
}
/**
 * MOSTRAR FORMULARIO DE ENTREGA
 */
public function mostrarEntrega($idEvaluacion)
{
    if (!session('usuario')) {
        return redirect('/login');
    }

    $usuario = session('usuario');

    try {
        // Obtener información de la evaluación
        $evaluacion = DB::selectOne("
            SELECT 
                e.*,
                m.Nombre as modulo_nombre,
                c.Titulo as curso_titulo,
                c.Id_curso
            FROM evaluaciones e
            INNER JOIN modulos m ON e.Id_modulo = m.Id_modulo
            INNER JOIN cursos c ON m.Id_curso = c.Id_curso
            WHERE e.Id_evaluacion = ?
        ", [$idEvaluacion]);

        if (!$evaluacion) {
            return redirect('/estudiante/dashboard')->with('error', 'Evaluación no encontrada.');
        }

        // Verificar si ya hay una entrega
        $entregaExistente = DB::table('entregas')
            ->where('Id_evaluacion', $idEvaluacion)
            ->where('Id_usuario', $usuario->Id_usuario)
            ->first();

        return view('estudiante.entrega', [
            'usuario' => $usuario,
            'evaluacion' => $evaluacion,
            'entregaExistente' => $entregaExistente
        ]);

    } catch (\Exception $e) {
        Log::error('ERROR en mostrarEntrega: ' . $e->getMessage());
        return redirect('/estudiante/dashboard')->with('error', 'Error al cargar el formulario de entrega.');
    }
}
/**
 * PROCESAR ENTREGA DE TRABAJO
 */
public function procesarEntrega(Request $request, $idEvaluacion)
{
    if (!session('usuario')) {
        return redirect('/login');
    }

    $usuario = session('usuario');

    try {
        // Verificar si ya existe una entrega
        $entregaExistente = DB::table('entregas')
            ->where('Id_evaluacion', $idEvaluacion)
            ->where('Id_usuario', $usuario->Id_usuario)
            ->first();

        if ($entregaExistente) {
            return back()->with('error', 'Ya has realizado una entrega para esta evaluación.');
        }

        // Validar datos
        $request->validate([
            'descripcion' => 'required|string|min:10',
            'archivo' => 'nullable|file|max:10240|mimes:pdf,doc,docx,zip,rar,jpg,jpeg,png'
        ]);

        // Procesar archivo
        $nombreArchivo = null;
        if ($request->hasFile('archivo')) {
            $archivo = $request->file('archivo');
            $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
            $archivo->storeAs('entregas', $nombreArchivo, 'public');
        }

        // Crear entrega
        DB::table('entregas')->insert([
            'Id_evaluacion' => $idEvaluacion,
            'Id_usuario' => $usuario->Id_usuario,
            'Archivo' => $nombreArchivo,
            'Descripcion' => $request->descripcion,
            'Fecha_entrega' => now(),
            'Estado' => 'pendiente',
            'Puntos_asignados' => 0
        ]);

        Log::info("✅ Entrega creada - Usuario: $usuario->Id_usuario, Evaluación: $idEvaluacion");

        return redirect("/estudiante/entrega/$idEvaluacion")
            ->with('success', '¡Trabajo enviado correctamente! El docente lo revisará y asignará una calificación.');

    } catch (\Exception $e) {
        Log::error('❌ ERROR procesando entrega: ' . $e->getMessage());
        return back()->with('error', 'Error al enviar el trabajo: ' . $e->getMessage());
    }
}
// En EstudianteController.php - Agrega estos métodos
////////////////////
/**
 * Mostrar evaluación para el estudiante
 */
public function verEvaluacion($idEvaluacion)
{
    if (!session('usuario')) {
        return redirect('/login');
    }

    try {
        $usuario = session('usuario');
        $idUsuario = $usuario->Id_usuario;

        // Obtener la evaluación con información del curso y módulo
        $evaluacion = DB::table('evaluaciones as e')
            ->select('e.*', 'm.Nombre as modulo_nombre', 'm.Id_curso', 'c.Titulo as curso_titulo')
            ->join('modulos as m', 'e.Id_modulo', '=', 'm.Id_modulo')
            ->join('cursos as c', 'm.Id_curso', '=', 'c.Id_curso')
            ->where('e.Id_evaluacion', $idEvaluacion)
            ->first();

        if (!$evaluacion) {
            return back()->with('error', 'Evaluación no encontrada.');
        }

        // Verificar si el estudiante está inscrito en el curso
        $inscripcion = DB::table('inscripciones')
            ->where('Id_curso', $evaluacion->Id_curso)
            ->where('Id_usuario', $idUsuario)
            ->where('Estado', 1)
            ->first();

        if (!$inscripcion) {
            return back()->with('error', 'No estás inscrito en este curso.');
        }

        // Verificar si ya completó esta evaluación
        $progreso = DB::table('progreso_evaluacion')
            ->where('Id_evaluacion', $idEvaluacion)
            ->where('Id_usuario', $idUsuario)
            ->first();

        if ($progreso && $progreso->Aprobado) {
            return redirect()->route('estudiante.evaluacion.resultado', ['id' => $idEvaluacion])
                ->with('info', 'Ya completaste esta evaluación.');
        }

        return view('estudiante.evaluacion', [
            'usuario' => $usuario,
            'evaluacion' => $evaluacion,
            'progreso' => $progreso
        ]);

    } catch (\Exception $e) {
        Log::error('Error al cargar evaluación: ' . $e->getMessage());
        return back()->with('error', 'Error al cargar la evaluación.');
    }
}

/**
 * Procesar la evaluación enviada por el estudiante
 */
public function procesarEvaluacion($idEvaluacion, Request $request)
{
    if (!session('usuario')) {
        return redirect('/login');
    }

    DB::beginTransaction();

    try {
        $usuario = session('usuario');
        $idUsuario = $usuario->Id_usuario;

        // Obtener la evaluación
        $evaluacion = DB::table('evaluaciones')
            ->where('Id_evaluacion', $idEvaluacion)
            ->first();

        if (!$evaluacion) {
            return back()->with('error', 'Evaluación no encontrada.');
        }

        // Calcular puntaje (simulado por ahora)
        $puntajeObtenido = $this->calcularPuntajeEvaluacion($request->all(), $evaluacion);
        $porcentaje = ($puntajeObtenido / $evaluacion->Puntaje_maximo) * 100;
        $aprobado = $porcentaje >= 60; // 60% para aprobar

        // Guardar progreso de evaluación
        DB::table('progreso_evaluacion')->insert([
            'Id_evaluacion' => $idEvaluacion,
            'Id_usuario' => $idUsuario,
            'Puntaje_obtenido' => $puntajeObtenido,
            'Porcentaje' => $porcentaje,
            'Aprobado' => $aprobado,
            'Fecha_completado' => now()
        ]);

        // Asignar puntos si está aprobado
        if ($aprobado) {
            $this->asignarPuntosEvaluacion($idUsuario, $idEvaluacion, $puntajeObtenido);
        }

        DB::commit();

        return redirect()->route('estudiante.evaluacion.resultado', ['id' => $idEvaluacion])
            ->with('success', 'Evaluación completada exitosamente.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error al procesar evaluación: ' . $e->getMessage());
        return back()->with('error', 'Error al procesar la evaluación: ' . $e->getMessage());
    }
}

/**
 * Mostrar resultado de la evaluación
 */
public function verResultadoEvaluacion($idEvaluacion)
{
    if (!session('usuario')) {
        return redirect('/login');
    }

    try {
        $usuario = session('usuario');
        $idUsuario = $usuario->Id_usuario;

        // Obtener evaluación y progreso
        $evaluacion = DB::table('evaluaciones as e')
            ->select('e.*', 'm.Nombre as modulo_nombre', 'c.Titulo as curso_titulo')
            ->join('modulos as m', 'e.Id_modulo', '=', 'm.Id_modulo')
            ->join('cursos as c', 'm.Id_curso', '=', 'c.Id_curso')
            ->where('e.Id_evaluacion', $idEvaluacion)
            ->first();

        $progreso = DB::table('progreso_evaluacion')
            ->where('Id_evaluacion', $idEvaluacion)
            ->where('Id_usuario', $idUsuario)
            ->first();

        if (!$progreso) {
            return redirect()->route('estudiante.evaluacion.ver', ['id' => $idEvaluacion])
                ->with('error', 'Debes completar la evaluación primero.');
        }

        return view('estudiante.resultado_evaluacion', [
            'usuario' => $usuario,
            'evaluacion' => $evaluacion,
            'progreso' => $progreso
        ]);

    } catch (\Exception $e) {
        Log::error('Error al cargar resultado: ' . $e->getMessage());
        return back()->with('error', 'Error al cargar el resultado.');
    }
}

/**
 * Método auxiliar para calcular puntaje (simulado)
 */
private function calcularPuntajeEvaluacion($respuestas, $evaluacion)
{
    // Aquí implementas tu lógica de corrección real
    // Por ahora, devolvemos un puntaje simulado basado en respuestas aleatorias
    
    $puntajeBase = $evaluacion->Puntaje_maximo * 0.7; // 70% base
    $variacion = rand(-10, 20); // Variación de -10% a +20%
    
    return min($evaluacion->Puntaje_maximo, max(0, $puntajeBase + $variacion));
}

/**
 * Método auxiliar para asignar puntos por evaluación aprobada
 */
private function asignarPuntosEvaluacion($idUsuario, $idEvaluacion, $puntaje)
{
    try {
        // 20 puntos por evaluación aprobada
        $puntos = 20;
        
        // Actualizar o insertar en gestion_puntos
        $gestionPuntos = DB::table('gestion_puntos')
            ->where('Id_usuario', $idUsuario)
            ->first();
            
        if ($gestionPuntos) {
            // Actualizar existente
            DB::table('gestion_puntos')
                ->where('Id_usuario', $idUsuario)
                ->update([
                    'Total_puntos_acumulados' => $gestionPuntos->Total_puntos_acumulados + $puntos,
                    'Total_puntos_actual' => $gestionPuntos->Total_puntos_actual + $puntos,
                    'updated_at' => now()
                ]);
        } else {
            // Insertar nuevo
            DB::table('gestion_puntos')->insert([
                'Id_usuario' => $idUsuario,
                'Total_puntos_acumulados' => $puntos,
                'Total_puntos_actual' => $puntos,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Actualizar ranking
        $this->actualizarRanking($idUsuario);

    } catch (\Exception $e) {
        Log::error('Error al asignar puntos evaluación: ' . $e->getMessage());
    }
}

/**
 * Método auxiliar para actualizar ranking
 */
private function actualizarRanking($idUsuario)
{
    try {
        // Obtener puntos actuales
        $gestionPuntos = DB::table('gestion_puntos')
            ->where('Id_usuario', $idUsuario)
            ->first();
            
        if ($gestionPuntos) {
            $periodo = date('Y-m'); // Periodo mensual
            
            // Actualizar o insertar en ranking
            DB::table('ranking')->updateOrInsert(
                [
                    'Id_usuario' => $idUsuario,
                    'Periodo' => $periodo
                ],
                [
                    'Total_puntos_acumulados' => $gestionPuntos->Total_puntos_acumulados,
                    'updated_at' => now()
                ]
            );
        }
        
    } catch (\Exception $e) {
        Log::error('Error al actualizar ranking: ' . $e->getMessage());
    }
}
}