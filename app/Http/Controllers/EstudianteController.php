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
            // PRIMERO: SINCRONIZAR AUTOMÁTICAMENTE
            $this->sincronizarAutomaticamente($usuario->Id_usuario);

            // LUEGO: OBTENER DATOS CON RELACIÓN GARANTIZADA
            $cursos_inscritos = DB::select("
                SELECT 
                    c.Id_curso,
                    c.Titulo,
                    c.Descripcion, 
                    c.Duracion,
                    c.Costo,
                    i.Id_inscripcion,
                    i.Fecha_inscripcion,
                    COALESCE(pc.Porcentaje, 0) as Porcentaje,
                    COALESCE(pc.Nivel, 1) as Nivel,
                    COALESCE(pc.Modulos_completados, 0) as Modulos_completados,
                    COALESCE(pc.Temas_completados, 0) as Temas_completados,
                    COALESCE(pc.Evaluaciones_superadas, 0) as Evaluaciones_superadas,
                    COALESCE(pc.Actividades_superadas, 0) as Actividades_superadas,
                    (SELECT COUNT(*) FROM modulos m WHERE m.Id_curso = c.Id_curso) as total_modulos,
                    (SELECT COUNT(*) FROM evaluaciones e 
                     INNER JOIN modulos m ON e.Id_modulo = m.Id_modulo 
                     WHERE m.Id_curso = c.Id_curso) as total_evaluaciones
                FROM inscripciones i
                INNER JOIN cursos c ON i.Id_curso = c.Id_curso
                INNER JOIN progreso_curso pc ON (pc.Id_usuario = i.Id_usuario AND pc.Id_curso = i.Id_curso)
                WHERE i.Id_usuario = ?
                AND i.Estado = 1
                ORDER BY i.Fecha_inscripcion DESC, c.Titulo
            ", [$usuario->Id_usuario]);

            $cursos_inscritos = collect($cursos_inscritos);

            Log::info("✅ Dashboard sincronizado - Usuario {$usuario->Id_usuario}: {$cursos_inscritos->count()} cursos");

            // Resto del código del dashboard...
            $cursos_disponibles = DB::table('cursos as c')
                ->whereNotExists(function ($query) use ($usuario) {
                    $query->select(DB::raw(1))
                          ->from('inscripciones as i')
                          ->whereColumn('i.Id_curso', 'c.Id_curso')
                          ->where('i.Id_usuario', $usuario->Id_usuario)
                          ->where('i.Estado', 1);
                })
                ->get();

            $evaluaciones_pendientes = collect($this->obtenerEvaluacionesPendientes($usuario->Id_usuario));
            
            $gestion_puntos = DB::table('gestion_puntos')
                ->where('Id_usuario', $usuario->Id_usuario)
                ->first();

            $ranking_actual = $this->obtenerRankingActual($usuario->Id_usuario);
            
            $insignias = collect();

            $progreso_general = $this->calcularProgresoGeneral($cursos_inscritos);

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
                'progreso_general' => $this->getProgresoGeneralVacio()
            ]);
        }
    }

    /**
     * SINCRONIZACIÓN AUTOMÁTICA AL CARGAR DASHBOARD
     */
    private function sincronizarAutomaticamente($idUsuario)
    {
        try {
            // Verificar si necesita sincronización
            $necesitaSincronizacion = DB::selectOne("
                SELECT 
                    EXISTS(
                        SELECT 1 FROM inscripciones i 
                        WHERE i.Id_usuario = ? 
                        AND i.Estado = 1 
                        AND NOT EXISTS (
                            SELECT 1 FROM progreso_curso pc 
                            WHERE pc.Id_usuario = i.Id_usuario 
                            AND pc.Id_curso = i.Id_curso
                        )
                    ) as necesita_sincronizacion
            ", [$idUsuario])->necesita_sincronizacion;

            if ($necesitaSincronizacion) {
                Log::info("🔄 Sincronización automática para usuario: $idUsuario");
                $this->sincronizarInscripcionesProgreso($idUsuario);
            }

        } catch (\Exception $e) {
            Log::error('ERROR en sincronización automática: ' . $e->getMessage());
        }
    }

    /**
     * SINCRONIZACIÓN MANUAL DESDE INTERFAZ
     */
/**
 * SINCRONIZACIÓN CON VERIFICACIÓN DE EXISTENCIA - CORREGIDO
 */
public function sincronizarInscripcionesProgreso($idUsuario = null)
{
    try {
        DB::beginTransaction();

        $condicionUsuario = $idUsuario ? "AND i.Id_usuario = $idUsuario" : "";

        // 1. CREAR PROGRESO PARA INSCRIPCIONES FALTANTES (SOLO SI NO EXISTE)
        $creados = DB::insert("
            INSERT INTO progreso_curso (Id_usuario, Id_curso, Id_inscripcion, Fecha_actualizacion, Porcentaje, Nivel, Modulos_completados, Temas_completados, Evaluaciones_superadas, Actividades_superadas)
            SELECT 
                i.Id_usuario,
                i.Id_curso,
                i.Id_inscripcion,
                NOW(),
                0,
                1,
                0,
                0,
                0,
                0
            FROM inscripciones i
            WHERE i.Estado = 1
            $condicionUsuario
            AND NOT EXISTS (
                SELECT 1 FROM progreso_curso pc 
                WHERE pc.Id_usuario = i.Id_usuario 
                AND pc.Id_curso = i.Id_curso
            )
        ");

        // 2. ELIMINAR PROGRESO SIN INSCRIPCIÓN
        $eliminados = DB::delete("
            DELETE FROM progreso_curso 
            WHERE NOT EXISTS (
                SELECT 1 FROM inscripciones i 
                WHERE i.Id_usuario = progreso_curso.Id_usuario 
                AND i.Id_curso = progreso_curso.Id_curso
                AND i.Estado = 1
            )
            $condicionUsuario
        ");

        DB::commit();

        Log::info("✅ Sincronización completada - Creados: $creados, Eliminados: $eliminados");

        return [
            'success' => true,
            'creados' => $creados,
            'eliminados' => $eliminados
        ];

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('ERROR en sincronización: ' . $e->getMessage());
        return ['success' => false, 'error' => $e->getMessage()];
    }
}
/**
 * ELIMINAR INSCRIPCIÓN Y PROGRESO - SINCRONIZADO
 */
public function eliminarInscripcion($idCurso)
{
    if (!session('usuario')) {
        return redirect('/login');
    }

    $usuario = session('usuario');

    try {
        DB::beginTransaction();

        // 1. Eliminar progreso primero
        DB::table('progreso_curso')
            ->where('Id_usuario', $usuario->Id_usuario)
            ->where('Id_curso', $idCurso)
            ->delete();

        // 2. Eliminar inscripción
        DB::table('inscripciones')
            ->where('Id_usuario', $usuario->Id_usuario)
            ->where('Id_curso', $idCurso)
            ->delete();

        DB::commit();

        Log::info("✅ Inscripción y progreso eliminados - Usuario: {$usuario->Id_usuario}, Curso: $idCurso");

        return redirect()->route('estudiante.dashboard')
            ->with('success', 'Inscripción eliminada correctamente.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('ERROR eliminando inscripción: ' . $e->getMessage());
        return redirect()->route('estudiante.dashboard')
            ->with('error', 'Error al eliminar la inscripción: ' . $e->getMessage());
    }
}

/**
 * MÉTODO PARA ELIMINAR PROGRESO DUPLICADO MANUALMENTE
 */
public function eliminarProgresoDuplicado($idUsuario, $idCurso)
{
    try {
        DB::beginTransaction();

        // Encontrar el ID del progreso más reciente
        $progresoReciente = DB::table('progreso_curso')
            ->where('Id_usuario', $idUsuario)
            ->where('Id_curso', $idCurso)
            ->orderBy('Fecha_actualizacion', 'DESC')
            ->first();

        if ($progresoReciente) {
            // Eliminar todos excepto el más reciente
            $eliminados = DB::table('progreso_curso')
                ->where('Id_usuario', $idUsuario)
                ->where('Id_curso', $idCurso)
                ->where('Id_progreso', '!=', $progresoReciente->Id_progreso)
                ->delete();

            DB::commit();
            
            Log::info("✅ Duplicados eliminados: $eliminados, Mantenido: {$progresoReciente->Id_progreso}");
            
            return [
                'success' => true,
                'eliminados' => $eliminados,
                'mantenido' => $progresoReciente->Id_progreso
            ];
        }

        DB::commit();
        return ['success' => true, 'eliminados' => 0];

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('ERROR eliminando duplicados: ' . $e->getMessage());
        return ['success' => false, 'error' => $e->getMessage()];
    }
}
    /**
     * INSCRIPCIÓN CON GARANTÍA DE RELACIÓN
     */
  // En EstudianteController - método inscribirseCurso ACTUALIZADO
public function inscribirseCurso(Request $request, $idCurso)
{
    if (!session('usuario')) {
        return redirect('/login');
    }

    $usuario = session('usuario');

    try {
        DB::beginTransaction();

        // Verificar curso y cupos
        $curso = DB::table('cursos')->where('Id_curso', $idCurso)->first();
        if (!$curso) {
            throw new \Exception('El curso no existe.');
        }

        // VERIFICAR CUPOS DISPONIBLES
        if (isset($curso->Cupos_disponibles) && $curso->Cupos_disponibles <= 0) {
            throw new \Exception('Lo sentimos, este curso ya no tiene cupos disponibles.');
        }

        // Verificar inscripción existente
        $inscripcionExistente = DB::table('inscripciones')
            ->where('Id_usuario', $usuario->Id_usuario)
            ->where('Id_curso', $idCurso)
            ->where('Estado', 1)
            ->first();

        if ($inscripcionExistente) {
            // Verificar si ya existe progreso
            $progresoExistente = DB::table('progreso_curso')
                ->where('Id_usuario', $usuario->Id_usuario)
                ->where('Id_curso', $idCurso)
                ->first();

            if ($progresoExistente) {
                throw new \Exception('Ya estás inscrito y tienes progreso en este curso.');
            } else {
                // Solo falta el progreso, crearlo
                DB::table('progreso_curso')->insert([
                    'Id_usuario' => $usuario->Id_usuario,
                    'Id_curso' => $idCurso,
                    'Id_inscripcion' => $inscripcionExistente->Id_inscripcion,
                    'Fecha_actualizacion' => now(),
                    'Porcentaje' => 0,
                    'Nivel' => 1,
                    'Modulos_completados' => 0,
                    'Temas_completados' => 0,
                    'Evaluaciones_superadas' => 0,
                    'Actividades_superadas' => 0
                ]);
                
                DB::commit();
                Log::info("✅ Progreso creado para inscripción existente - Usuario: {$usuario->Id_usuario}, Curso: $idCurso");
                
                return redirect()->route('estudiante.explorar_cursos')
                    ->with('success', '¡Progreso del curso activado exitosamente!');
            }
        }

        // 1. CREAR NUEVA INSCRIPCIÓN
        $idInscripcion = DB::table('inscripciones')->insertGetId([
            'Id_usuario' => $usuario->Id_usuario,
            'Id_curso' => $idCurso,
            'Fecha_inscripcion' => now()->format('Y-m-d'),
            'Estado' => 1
        ]);

        Log::info("✅ Inscripción creada con ID: $idInscripcion");

        // 2. ACTUALIZAR CUPOS DEL CURSO
        if (isset($curso->Cupos_disponibles)) {
            $nuevosCuposDisponibles = $curso->Cupos_disponibles - 1;
            $estadoCupos = $nuevosCuposDisponibles > 0 ? 'disponible' : 'completo';
            
            DB::table('cursos')
                ->where('Id_curso', $idCurso)
                ->update([
                    'Cupos_disponibles' => $nuevosCuposDisponibles,
                    'Estado_cupos' => $estadoCupos
                ]);
            
            Log::info("✅ Cupos actualizados: $nuevosCuposDisponibles disponibles");
        }

        // 3. VERIFICAR QUE NO EXISTA PROGRESO ANTES DE INSERTAR
        $progresoExistente = DB::table('progreso_curso')
            ->where('Id_usuario', $usuario->Id_usuario)
            ->where('Id_curso', $idCurso)
            ->exists();

        if (!$progresoExistente) {
            DB::table('progreso_curso')->insert([
                'Id_usuario' => $usuario->Id_usuario,
                'Id_curso' => $idCurso,
                'Id_inscripcion' => $idInscripcion,
                'Fecha_actualizacion' => now(),
                'Porcentaje' => 0,
                'Nivel' => 1,
                'Modulos_completados' => 0,
                'Temas_completados' => 0,
                'Evaluaciones_superadas' => 0,
                'Actividades_superadas' => 0
            ]);
            Log::info("✅ Nuevo progreso creado");
        } else {
            Log::warning("⚠️ Progreso ya existía para usuario {$usuario->Id_usuario} en curso $idCurso");
        }

        DB::commit();

        Log::info("✅ Inscripción y progreso procesados - Usuario: {$usuario->Id_usuario}, Curso: $idCurso");

        return redirect()->route('estudiante.explorar_cursos')
            ->with('success', '¡Te has inscrito al curso exitosamente!');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('❌ ERROR en inscripción: ' . $e->getMessage());
        return redirect()->route('estudiante.explorar_cursos')
            ->with('error', $e->getMessage());
    }
}
    /**
     * MÉTODO PARA REPARACIÓN MASIVA DESDE INTERFAZ
     */
    public function repararBaseDeDatos()
    {
        try {
            Log::info("🔧 INICIANDO REPARACIÓN MASIVA DE BASE DE DATOS");

            // Reparar todos los usuarios
            $usuarios = DB::table('usuarios')->pluck('Id_usuario');
            $resultados = [];

            foreach ($usuarios as $usuarioId) {
                $resultado = $this->sincronizarInscripcionesProgreso($usuarioId);
                $resultados[$usuarioId] = $resultado;
            }

            Log::info("✅ REPARACIÓN MASIVA COMPLETADA");

            return response()->json([
                'success' => true,
                'message' => 'Base de datos reparada completamente',
                'usuarios_procesados' => count($usuarios),
                'resultados' => $resultados
            ]);

        } catch (\Exception $e) {
            Log::error('❌ ERROR en reparación masiva: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    /**
     * MÉTODO CALCULAR PROGRESO GENERAL - CORREGIDO
     */
    private function calcularProgresoGeneral($cursos_inscritos)
    {
        try {
            // Asegurarnos de que es una colección
            if (!$cursos_inscritos instanceof \Illuminate\Support\Collection) {
                $cursos_inscritos = collect($cursos_inscritos);
            }

            $cursos_completados = $cursos_inscritos->where('Porcentaje', 100)->count();
            $total_cursos = $cursos_inscritos->count();
            $promedio_progreso = $total_cursos > 0 ? $cursos_inscritos->avg('Porcentaje') : 0;

            Log::info("📈 Progreso general calculado: {$total_cursos} cursos, {$cursos_completados} completados, {$promedio_progreso}% promedio");

            return [
                'total_cursos' => $total_cursos,
                'cursos_completados' => $cursos_completados,
                'promedio_progreso' => round($promedio_progreso, 1),
                'total_evaluaciones' => $cursos_inscritos->sum('total_evaluaciones') ?? 0,
                'total_actividades' => $cursos_inscritos->sum('Actividades_superadas') ?? 0,
                'total_modulos' => $cursos_inscritos->sum('total_modulos') ?? 0,
                'total_evaluaciones_pendientes' => 0,
                'total_cursos_disponibles' => 0 // Este se calcula aparte si es necesario
            ];

        } catch (\Exception $e) {
            Log::error('ERROR en calcularProgresoGeneral: ' . $e->getMessage());
            return $this->getProgresoGeneralVacio();
        }
    }

    /**
     * PROGRESO GENERAL VACÍO
     */
    private function getProgresoGeneralVacio()
    {
        return [
            'total_cursos' => 0,
            'cursos_completados' => 0,
            'promedio_progreso' => 0,
            'total_evaluaciones' => 0,
            'total_actividades' => 0,
            'total_modulos' => 0,
            'total_evaluaciones_pendientes' => 0,
            'total_cursos_disponibles' => 0
        ];
    }

    /**
     * OBTENER EVALUACIONES PENDIENTES
     */
    private function obtenerEvaluacionesPendientes($usuarioId)
    {
        try {
            return DB::select("
                SELECT 
                    e.Id_evaluacion,
                    e.Tipo,
                    e.Puntaje_maximo,
                    e.Fecha_fin,
                    c.Titulo as curso_titulo,
                    m.Nombre as modulo_nombre
                FROM evaluaciones e
                INNER JOIN modulos m ON e.Id_modulo = m.Id_modulo
                INNER JOIN cursos c ON m.Id_curso = c.Id_curso
                INNER JOIN inscripciones i ON c.Id_curso = i.Id_curso
                WHERE i.Id_usuario = ?
                AND i.Estado = 1
                AND NOT EXISTS (
                    SELECT 1 
                    FROM progreso_evaluacion pe 
                    WHERE pe.Id_evaluacion = e.Id_evaluacion 
                    AND pe.Id_usuario = i.Id_usuario
                    AND pe.Aprobado = 1
                )
                AND e.Fecha_fin > NOW()
                ORDER BY e.Fecha_fin ASC
            ", [$usuarioId]);
        } catch (\Exception $e) {
            Log::error('Error obteniendo evaluaciones pendientes: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * OBTENER RANKING ACTUAL
     */
    private function obtenerRankingActual($usuarioId)
    {
        try {
            $periodo = date('Y-m');
            return DB::table('ranking')
                ->where('Id_usuario', $usuarioId)
                ->where('Periodo', $periodo)
                ->select('Posicion')
                ->first();
        } catch (\Exception $e) {
            Log::error('Error obteniendo ranking: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * MÉTODO PARA VERIFICAR ESTADO DE RELACIONES
     */
    public function estadoRelaciones($idUsuario = null)
    {
        if (!$idUsuario) {
            $idUsuario = session('usuario')->Id_usuario ?? 4;
        }

        try {
            $estado = [];

            // 1. Inscripciones vs Progreso
            $estado['inscripciones_progreso'] = DB::select("
                SELECT 
                    (SELECT COUNT(*) FROM inscripciones WHERE Id_usuario = ? AND Estado = 1) as total_inscripciones,
                    (SELECT COUNT(DISTINCT Id_curso) FROM progreso_curso WHERE Id_usuario = ?) as total_progreso,
                    (SELECT COUNT(*) FROM inscripciones i 
                     WHERE i.Id_usuario = ? AND i.Estado = 1 
                     AND NOT EXISTS (
                         SELECT 1 FROM progreso_curso pc 
                         WHERE pc.Id_usuario = i.Id_usuario AND pc.Id_curso = i.Id_curso
                     )) as faltantes_progreso
            ", [$idUsuario, $idUsuario, $idUsuario])[0];

            // 2. Cursos inscritos con detalles
            $estado['cursos_detallados'] = DB::select("
                SELECT 
                    c.Id_curso,
                    c.Titulo,
                    i.Estado as inscripcion_estado,
                    CASE WHEN pc.Id_progreso IS NOT NULL THEN 'SI' ELSE 'NO' END as tiene_progreso,
                    COALESCE(pc.Porcentaje, 0) as progreso_porcentaje
                FROM inscripciones i
                INNER JOIN cursos c ON i.Id_curso = c.Id_curso
                LEFT JOIN progreso_curso pc ON (pc.Id_usuario = i.Id_usuario AND pc.Id_curso = i.Id_curso)
                WHERE i.Id_usuario = ?
                ORDER BY c.Titulo
            ", [$idUsuario]);

            return response()->json([
                'success' => true,
                'usuario_id' => $idUsuario,
                'estado' => $estado
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * MÉTODO PARA REPARAR TODO EL SISTEMA
     */
    public function repararSistemaCompleto()
    {
        try {
            Log::info("🛠️ INICIANDO REPARACIÓN COMPLETA DEL SISTEMA");

            // 1. Sincronizar todos los usuarios
            $usuarios = DB::table('usuarios')->get();
            $resultados = [];

            foreach ($usuarios as $usuario) {
                $resultado = $this->sincronizarInscripcionesProgreso($usuario->Id_usuario);
                $resultados[] = [
                    'usuario' => $usuario->Id_usuario,
                    'nombre' => $usuario->Nombre,
                    'success' => true
                ];
            }

            // 2. Actualizar puntos globales
            $this->actualizarPuntosGlobales();

            // 3. Actualizar ranking
            $this->actualizarRankingGlobal();

            Log::info("✅ REPARACIÓN COMPLETA FINALIZADA");

            return response()->json([
                'success' => true,
                'message' => 'Sistema reparado completamente',
                'usuarios_procesados' => count($usuarios),
                'detalles' => $resultados
            ]);

        } catch (\Exception $e) {
            Log::error('❌ ERROR en reparación completa: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    private function actualizarPuntosGlobales()
    {
        // Lógica para actualizar puntos de todos los usuarios
        $usuarios = DB::table('usuarios')->get();
        
        foreach ($usuarios as $usuario) {
            $puntos = $this->calcularPuntosRealesSimple($usuario->Id_usuario);
            DB::table('gestion_puntos')->updateOrInsert(
                ['Id_usuario' => $usuario->Id_usuario],
                [
                    'Total_puntos_actual' => $puntos,
                    'Total_puntos_acumulados' => $puntos,
                    'updated_at' => now()
                ]
            );
        }
    }

    private function actualizarRankingGlobal()
    {
        $periodo = date('Y-m');
        DB::table('ranking')->where('Periodo', $periodo)->delete();

        $usuariosConPuntos = DB::table('gestion_puntos')
            ->orderBy('Total_puntos_acumulados', 'DESC')
            ->get();

        $posicion = 1;
        foreach ($usuariosConPuntos as $usuario) {
            DB::table('ranking')->insert([
                'Id_usuario' => $usuario->Id_usuario,
                'Periodo' => $periodo,
                'Total_puntos_acumulados' => $usuario->Total_puntos_acumulados,
                'Posicion' => $posicion,
                'created_at' => now()
            ]);
            $posicion++;
        }
    }


    /**
     * MÉTODO ACTUALIZAR RANKING QUE FALTABA
     */
    private function actualizarRanking($idUsuario)
    {
        try {
            $gestionPuntos = DB::table('gestion_puntos')
                ->where('Id_usuario', $idUsuario)
                ->first();
                
            if ($gestionPuntos) {
                $periodo = date('Y-m');
                
                // Verificar si ya existe en el ranking
                $rankingExistente = DB::table('ranking')
                    ->where('Id_usuario', $idUsuario)
                    ->where('Periodo', $periodo)
                    ->first();

                if ($rankingExistente) {
                    // Actualizar existente
                    DB::table('ranking')
                        ->where('Id_ranking', $rankingExistente->Id_ranking)
                        ->update([
                            'Total_puntos_acumulados' => $gestionPuntos->Total_puntos_acumulados,
                            'updated_at' => now()
                        ]);
                } else {
                    // Insertar nuevo
                    DB::table('ranking')->insert([
                        'Id_usuario' => $idUsuario,
                        'Periodo' => $periodo,
                        'Total_puntos_acumulados' => $gestionPuntos->Total_puntos_acumulados,
                        'Posicion' => 999, // Temporal, se recalcula después
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }

                // Recalcular posiciones
                $this->recalcularPosicionesRanking($periodo);
                
                Log::info("✅ Ranking actualizado para usuario: $idUsuario");
            }
            
        } catch (\Exception $e) {
            Log::error('ERROR en actualizarRanking: ' . $e->getMessage());
        }
    }

  



    /**
     * MÉTODO PARA OBTENER PUNTOS ACTUALES (API)
     */
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
            Log::error('Error en obtenerPuntosActuales: ' . $e->getMessage());
            return response()->json([
                'puntos_actual' => 0,
                'puntos_acumulados' => 0
            ]);
        }
    }

    // ... (MANTENER TODOS LOS OTROS MÉTODOS QUE YA FUNCIONAN BIEN)
    // explorarCursos(), inscribirseCurso(), verCurso(), completarTema(), etc.
    // Estos métodos parecen estar funcionando correctamente
// En EstudianteController - método explorarCursos ACTUALIZADO
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

        // Luego obtener todos los cursos CON INFORMACIÓN DE CUPOS
        $todos_cursos = DB::table('cursos as c')
            ->select(
                'c.Id_curso',
                'c.Titulo',
                'c.Descripcion',
                'c.Duracion',
                'c.Costo',
                'c.Cupos_totales',
                'c.Cupos_disponibles',
                'c.Estado_cupos',
                DB::raw('(SELECT COUNT(*) FROM modulos m WHERE m.Id_curso = c.Id_curso) as total_modulos'),
                DB::raw('(SELECT COUNT(*) FROM evaluaciones e INNER JOIN modulos m ON e.Id_modulo = m.Id_modulo WHERE m.Id_curso = c.Id_curso) as total_evaluaciones'),
                DB::raw('(SELECT COUNT(*) FROM inscripciones i WHERE i.Id_curso = c.Id_curso AND i.Estado = 1) as total_estudiantes'),
                DB::raw('CASE WHEN c.Id_curso IN (' . ($cursos_inscritos_ids ? implode(',', $cursos_inscritos_ids) : '0') . ') THEN 1 ELSE 0 END as ya_inscrito')
            )
            ->orderBy('c.Titulo')
            ->get();

        // Separar cursos
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
     * MÉTODO PARA FIX RÁPIDO DEL SISTEMA
     */
    public function fixSistemaRapido()
    {
        try {
            Log::info("🔧 EJECUTANDO FIX RÁPIDO DEL SISTEMA");
            
            // 1. Actualizar puntos basados en progreso real
            $usuarios = DB::table('usuarios')->get();
            $actualizados = 0;
            
            foreach ($usuarios as $usuario) {
                $puntosReales = $this->calcularPuntosReales($usuario->Id_usuario);
                if ($puntosReales > 0) {
                    DB::table('gestion_puntos')->updateOrInsert(
                        ['Id_usuario' => $usuario->Id_usuario],
                        [
                            'Total_puntos_actual' => $puntosReales,
                            'Total_puntos_acumulados' => $puntosReales,
                            'updated_at' => now()
                        ]
                    );
                    $actualizados++;
                }
            }
            
            // 2. Recrear ranking completo
            $periodo = date('Y-m');
            DB::table('ranking')->where('Periodo', $periodo)->delete();
            
            $usuariosConPuntos = DB::table('gestion_puntos')
                ->orderBy('Total_puntos_acumulados', 'DESC')
                ->get();
            
            $posicion = 1;
            foreach ($usuariosConPuntos as $usuario) {
                DB::table('ranking')->insert([
                    'Id_usuario' => $usuario->Id_usuario,
                    'Periodo' => $periodo,
                    'Total_puntos_acumulados' => $usuario->Total_puntos_acumulados,
                    'Posicion' => $posicion,
                    'created_at' => now()
                ]);
                $posicion++;
            }
            
            Log::info("✅ FIX COMPLETADO: $actualizados usuarios actualizados, ranking con " . ($posicion-1) . " posiciones");
            
            return response()->json([
                'success' => true,
                'message' => "Sistema reparado: $actualizados usuarios actualizados",
                'ranking_actualizado' => $posicion-1
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ ERROR en fix rápido: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
     private function calcularPuntosReales($idUsuario)
    {
        try {
            Log::info("🔍 Calculando puntos reales para usuario: $idUsuario");

            // 1. Puntos por temas completados (10 puntos por tema)
            $temasCompletados = DB::table('progreso_tema')
                ->where('Completado', 1)
                ->count();
            $puntosTemas = $temasCompletados * 10;

            Log::info("   📚 Temas completados: $temasCompletados = $puntosTemas puntos");

            // 2. Puntos por módulos completados (50 puntos por módulo)
            $modulosCompletados = DB::table('progreso_curso')
                ->where('Id_usuario', $idUsuario)
                ->sum('Modulos_completados');
            $puntosModulos = $modulosCompletados * 50;

            Log::info("   📦 Módulos completados: $modulosCompletados = $puntosModulos puntos");

            // 3. Puntos por cursos completados (200 puntos por curso)
            $cursosCompletados = DB::table('progreso_curso')
                ->where('Id_usuario', $idUsuario)
                ->where('Porcentaje', '>=', 100)
                ->count();
            $puntosCursos = $cursosCompletados * 200;

            Log::info("   🎓 Cursos completados: $cursosCompletados = $puntosCursos puntos");

            // 4. Puntos por evaluaciones aprobadas (20 puntos por evaluación)
            $evaluacionesAprobadas = DB::table('progreso_evaluacion')
                ->where('Id_usuario', $idUsuario)
                ->where('Aprobado', 1)
                ->count();
            $puntosEvaluaciones = $evaluacionesAprobadas * 20;

            Log::info("   📝 Evaluaciones aprobadas: $evaluacionesAprobadas = $puntosEvaluaciones puntos");

            $puntosTotales = $puntosTemas + $puntosModulos + $puntosCursos + $puntosEvaluaciones;

            Log::info("   💰 PUNTOS TOTALES CALCULADOS: $puntosTotales");

            return $puntosTotales;

        } catch (\Exception $e) {
            Log::error('❌ ERROR en calcularPuntosReales: ' . $e->getMessage());
            return 0;
        }
    }
    private function calcularPuntosRealesSimple($idUsuario)
    {
        try {
            // Usar métodos de Laravel en lugar de consultas SQL directas
            $puntosTemas = DB::table('progreso_tema')
                ->where('Completado', 1)
                ->count() * 10;

            $puntosModulos = DB::table('progreso_curso')
                ->where('Id_usuario', $idUsuario)
                ->sum('Modulos_completados') * 50;

            $puntosCursos = DB::table('progreso_curso')
                ->where('Id_usuario', $idUsuario)
                ->where('Porcentaje', '>=', 100)
                ->count() * 200;

            $puntosEvaluaciones = DB::table('progreso_evaluacion')
                ->where('Id_usuario', $idUsuario)
                ->where('Aprobado', 1)
                ->count() * 20;

            $puntosTotales = $puntosTemas + $puntosModulos + $puntosCursos + $puntosEvaluaciones;

            Log::info("💰 Puntos calculados (simple) para usuario $idUsuario: $puntosTotales");

            return $puntosTotales;

        } catch (\Exception $e) {
            Log::error('❌ ERROR en calcularPuntosRealesSimple: ' . $e->getMessage());
            return 0;
        }
    }

  
    /**
     * MÉTODO EXTRA SEGURO PARA CALCULAR PUNTOS
     */
    private function calcularPuntosExtremadamenteSeguro($idUsuario)
    {
        try {
            $puntos = 0;
            
            // Puntos por temas con manejo de errores individual
            try {
                $temas = DB::table('progreso_tema')
                    ->where('Completado', 1)
                    ->count();
                $puntos += $temas * 10;
            } catch (\Exception $e) {
                Log::warning("Error calculando puntos de temas: " . $e->getMessage());
            }
            
            // Puntos por módulos
            try {
                $modulos = DB::table('progreso_curso')
                    ->where('Id_usuario', $idUsuario)
                    ->sum('Modulos_completados');
                $puntos += $modulos * 50;
            } catch (\Exception $e) {
                Log::warning("Error calculando puntos de módulos: " . $e->getMessage());
            }
            
            // Puntos por cursos
            try {
                $cursos = DB::table('progreso_curso')
                    ->where('Id_usuario', $idUsuario)
                    ->where('Porcentaje', '>=', 100)
                    ->count();
                $puntos += $cursos * 200;
            } catch (\Exception $e) {
                Log::warning("Error calculando puntos de cursos: " . $e->getMessage());
            }
            
            // Puntos por evaluaciones
            try {
                $evaluaciones = DB::table('progreso_evaluacion')
                    ->where('Id_usuario', $idUsuario)
                    ->where('Aprobado', 1)
                    ->count();
                $puntos += $evaluaciones * 20;
            } catch (\Exception $e) {
                Log::warning("Error calculando puntos de evaluaciones: " . $e->getMessage());
            }
            
            return $puntos;
            
        } catch (\Exception $e) {
            Log::error('ERROR crítico en calcularPuntosExtremadamenteSeguro: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * MÉTODO PARA VERIFICAR PUNTOS DE UN USUARIO ESPECÍFICO
     */
    public function debugUsuario($idUsuario = null)
    {
        if (!$idUsuario) {
            $idUsuario = session('usuario')->Id_usuario ?? 1;
        }

        try {
            $puntosCalculados = $this->calcularPuntosRealesSimple($idUsuario);
            $puntosExtremos = $this->calcularPuntosExtremadamenteSeguro($idUsuario);
            
            $gestionPuntos = DB::table('gestion_puntos')
                ->where('Id_usuario', $idUsuario)
                ->first();
                
            $usuario = DB::table('usuarios')
                ->where('Id_usuario', $idUsuario)
                ->first();
                
            $estadisticas = [
                'temas_completados' => DB::table('progreso_tema')->where('Completado', 1)->count(),
                'modulos_completados' => DB::table('progreso_curso')->where('Id_usuario', $idUsuario)->sum('Modulos_completados'),
                'cursos_completados' => DB::table('progreso_curso')->where('Id_usuario', $idUsuario)->where('Porcentaje', '>=', 100)->count(),
                'evaluaciones_aprobadas' => DB::table('progreso_evaluacion')->where('Id_usuario', $idUsuario)->where('Aprobado', 1)->count()
            ];

            return response()->json([
                'success' => true,
                'usuario' => $usuario,
                'puntos' => [
                    'calculados_simple' => $puntosCalculados,
                    'calculados_extremo' => $puntosExtremos,
                    'en_gestion_puntos' => $gestionPuntos ? $gestionPuntos->Total_puntos_acumulados : 0,
                    'diferencia' => $puntosCalculados - ($gestionPuntos ? $gestionPuntos->Total_puntos_acumulados : 0)
                ],
                'estadisticas' => $estadisticas,
                'gestion_puntos' => $gestionPuntos
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * INSCRIPCIÓN A CURSO 
     */
    
    /**
     * VER CURSO COMPLETO
     */
    // En EstudianteController - mejorar el método verCurso
// En EstudianteController - método verCurso ACTUALIZADO
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

        // Obtener módulos con sus temas y progreso del estudiante
        $modulos = DB::select("
            SELECT 
                m.*,
                (SELECT COUNT(*) FROM temas t WHERE t.Id_modulo = m.Id_modulo) as total_temas,
                (SELECT COUNT(*) FROM temas t 
                 INNER JOIN progreso_tema pt ON t.Id_tema = pt.Id_tema 
                 WHERE t.Id_modulo = m.Id_modulo 
                 AND pt.Completado = 1
                 AND pt.Id_usuario = ?) as temas_completados
            FROM modulos m
            WHERE m.Id_curso = ?
            ORDER BY m.Id_modulo
        ", [$usuario->Id_usuario, $idCurso]);

        // Obtener temas detallados con progreso
        $temas_detallados = [];
        foreach ($modulos as $modulo) {
            $temas = DB::select("
                SELECT 
                    t.*,
                    CASE WHEN pt.Completado = 1 THEN 1 ELSE 0 END as completado,
                    pt.Fecha_completado,
                    pt.Porcentaje as progreso_tema
                FROM temas t
                LEFT JOIN progreso_tema pt ON (t.Id_tema = pt.Id_tema AND pt.Id_usuario = ?)
                WHERE t.Id_modulo = ?
                ORDER BY t.Orden
            ", [$usuario->Id_usuario, $modulo->Id_modulo]);
            
            $modulo->temas = $temas;
        }

        // CALCULAR PORCENTAJE ACTUALIZADO - CONSIDERANDO MÓDULOS Y TEMAS
        $total_temas_curso = DB::selectOne("
            SELECT COUNT(*) as total FROM temas t
            INNER JOIN modulos m ON t.Id_modulo = m.Id_modulo
            WHERE m.Id_curso = ?
        ", [$idCurso])->total;

        $temas_completados_curso = DB::selectOne("
            SELECT COUNT(*) as completados
            FROM progreso_tema pt
            INNER JOIN temas t ON pt.Id_tema = t.Id_tema
            INNER JOIN modulos m ON t.Id_modulo = m.Id_modulo
            WHERE m.Id_curso = ? AND pt.Id_usuario = ? AND pt.Completado = 1
        ", [$idCurso, $usuario->Id_usuario])->completados;

        // Calcular porcentaje real
        $porcentaje_actual = $total_temas_curso > 0 ? 
            round(($temas_completados_curso / $total_temas_curso) * 100) : 0;
        $porcentaje_actual = min(100, $porcentaje_actual);

        // ACTUALIZAR EL PORCENTAJE EN LA BASE DE DATOS SI ES DIFERENTE
        if ($progreso->Porcentaje != $porcentaje_actual) {
            DB::table('progreso_curso')
                ->where('Id_progreso', $progreso->Id_progreso)
                ->update([
                    'Porcentaje' => $porcentaje_actual,
                    'Temas_completados' => $temas_completados_curso,
                    'Fecha_actualizacion' => now()
                ]);
            
            // Actualizar variable $progreso
            $progreso->Porcentaje = $porcentaje_actual;
            $progreso->Temas_completados = $temas_completados_curso;
            
            Log::info("📊 Porcentaje actualizado - Curso: $idCurso, Usuario: {$usuario->Id_usuario}, Porcentaje: $porcentaje_actual%");
        }

        // Obtener evaluaciones pendientes del curso
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
                AND pe.Id_usuario = ?
                AND pe.Aprobado = 1
            )
            ORDER BY e.Fecha_inicio ASC
        ", [$idCurso, $usuario->Id_usuario]);

        return view('estudiante.curso_detalle', compact(
            'usuario',
            'curso',
            'progreso',
            'modulos',
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
     * OBTENER PRÓXIMO TEMA PENDIENTE
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
     * COMPLETAR TEMA Y ASIGNAR PUNTOS
     */
   
    /**
     * VERIFICAR Y ASIGNAR PUNTOS POR MÓDULO COMPLETADO
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
     * ASIGNAR PUNTOS AL USUARIO
     */
    // En EstudianteController - método asignarPuntos ACTUALIZADO
// En EstudianteController - método asignarPuntos ACTUALIZADO
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
                'Total_saldo_usido' => 0,
                'puntos_canjeados' => 0,
                'Id_ranking' => null
            ]);

            Log::info("✅ Nuevo registro de puntos creado con $puntos puntos");
        }

        // ACTUALIZAR RANKING AUTOMÁTICAMENTE
        $this->actualizarRankingUsuario($idUsuario);

        return true;

    } catch (\Exception $e) {
        Log::error('❌ ERROR en asignarPuntos: ' . $e->getMessage());
        return false;
    }
}

// NUEVO MÉTODO: Actualizar ranking de un usuario específico
private function actualizarRankingUsuario($idUsuario)
{
    try {
        $periodo_actual = date('F Y'); // Ej: "Noviembre 2025"
        $periodo_date = date('Y-m-01'); // Primer día del mes
        
        // Obtener puntos actuales del usuario
        $gestion_puntos = DB::table('gestion_puntos')
            ->where('Id_usuario', $idUsuario)
            ->first();

        if (!$gestion_puntos) {
            Log::warning("⚠️ No se encontraron puntos para usuario $idUsuario");
            return;
        }

        $puntos_acumulados = $gestion_puntos->Total_puntos_acumulados;

        // Verificar si el usuario ya tiene ranking este mes
        $ranking_existente = DB::table('ranking')
            ->where('Id_usuario', $idUsuario)
            ->where('Periodo', $periodo_actual)
            ->first();

        if ($ranking_existente) {
            // Actualizar puntos existentes
            DB::table('ranking')
                ->where('Id_ranking', $ranking_existente->Id_ranking)
                ->update([
                    'Total_puntos_acumulados' => $puntos_acumulados
                ]);
            Log::info("✅ Ranking actualizado - Usuario: $idUsuario, Puntos: $puntos_acumulados");
        } else {
            // Crear nuevo registro de ranking
            DB::table('ranking')->insert([
                'Id_usuario' => $idUsuario,
                'Periodo' => $periodo_actual,
                'Total_puntos_acumulados' => $puntos_acumulados,
                'periodo_date' => $periodo_date,
                'Posicion' => 999 // Temporal, se recalcula después
            ]);
            Log::info("✅ Nuevo ranking creado - Usuario: $idUsuario, Puntos: $puntos_acumulados");
        }

        // Recalcular todas las posiciones del ranking
        $this->recalcularPosicionesRanking($periodo_actual);

    } catch (\Exception $e) {
        Log::error('❌ ERROR actualizando ranking usuario: ' . $e->getMessage());
    }
}

// NUEVO MÉTODO: Recalcular todas las posiciones del ranking
private function recalcularPosicionesRanking($periodo)
{
    try {
        Log::info("🔄 Recalculando posiciones del ranking para: $periodo");
        
        // Obtener todos los rankings del periodo ordenados por puntos (solo estudiantes)
        $rankings = DB::select("
            SELECT r.Id_ranking, r.Total_puntos_acumulados, r.Id_usuario
            FROM ranking r
            INNER JOIN usuarios u ON r.Id_usuario = u.Id_usuario
            INNER JOIN rol_usuario ru ON u.Id_usuario = ru.Id_usuario  
            INNER JOIN roles rol ON ru.Id_rol = rol.Id_rol
            WHERE r.Periodo = ? 
            AND rol.Nombre = 'Estudiante'
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

        Log::info("✅ Posiciones recalculadas: $periodo - $posicion participantes");

    } catch (\Exception $e) {
        Log::error('❌ ERROR recalculando posiciones: ' . $e->getMessage());
    }
}
    
    /**
     * VER ESTADO DE LA RELACIÓN INSCRIPCIONES-PROGRESO_CURSO
     */
    public function estadoRelacionesInscripcionesProgreso($idUsuario = null)
    {
        try {
            $condicionUsuario = $idUsuario ? "WHERE i.Id_usuario = $idUsuario" : "";

            $estado = DB::select("
                SELECT 
                    (SELECT COUNT(*) FROM inscripciones i $condicionUsuario AND i.Estado = 1) as total_inscripciones,
                    (SELECT COUNT(DISTINCT CONCAT(pc.Id_usuario, '-', pc.Id_curso)) FROM progreso_curso pc $condicionUsuario) as total_progreso,
                    (SELECT COUNT(*) FROM inscripciones i 
                     $condicionUsuario 
                     AND i.Estado = 1 
                     AND NOT EXISTS (
                         SELECT 1 FROM progreso_curso pc 
                         WHERE pc.Id_usuario = i.Id_usuario 
                         AND pc.Id_curso = i.Id_curso
                     )) as inscripciones_sin_progreso,
                    (SELECT COUNT(*) FROM progreso_curso pc 
                     $condicionUsuario 
                     AND NOT EXISTS (
                         SELECT 1 FROM inscripciones i 
                         WHERE i.Id_usuario = pc.Id_usuario 
                         AND i.Id_curso = pc.Id_curso
                         AND i.Estado = 1
                     )) as progreso_sin_inscripcion
            ")[0];

            // Detalle de inconsistencias
            $inconsistencias = DB::select("
                SELECT 
                    i.Id_inscripcion,
                    i.Id_usuario,
                    i.Id_curso,
                    u.Nombre,
                    u.Apellido,
                    c.Titulo as curso_titulo,
                    CASE 
                        WHEN pc.Id_progreso IS NULL THEN 'SIN PROGRESO'
                        ELSE 'CON PROGRESO'
                    END as estado
                FROM inscripciones i
                INNER JOIN usuarios u ON i.Id_usuario = u.Id_usuario
                INNER JOIN cursos c ON i.Id_curso = c.Id_curso
                LEFT JOIN progreso_curso pc ON (pc.Id_usuario = i.Id_usuario AND pc.Id_curso = i.Id_curso)
                WHERE i.Estado = 1
                $condicionUsuario
                ORDER BY i.Id_usuario, c.Titulo
            ");

            return response()->json([
                'success' => true,
                'usuario_id' => $idUsuario,
                'estado' => $estado,
                'inconsistencias' => $inconsistencias,
                'timestamp' => now()
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
 * VER EVALUACIÓN Y FORMULARIO DE ENTREGA
 */
public function verEvaluacion($idEvaluacion)
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

        // Verificar que el estudiante está inscrito en el curso
        $inscripcion = DB::table('inscripciones')
            ->where('Id_usuario', $usuario->Id_usuario)
            ->where('Id_curso', $evaluacion->Id_curso)
            ->where('Estado', 1)
            ->first();

        if (!$inscripcion) {
            return redirect('/estudiante/dashboard')->with('error', 'No estás inscrito en este curso.');
        }

        // Verificar si ya existe una entrega
        $entregaExistente = DB::table('entregas')
            ->where('Id_evaluacion', $idEvaluacion)
            ->where('Id_usuario', $usuario->Id_usuario)
            ->first();

        // Verificar progreso de evaluación
        $progresoEvaluacion = DB::table('progreso_evaluacion')
            ->where('Id_evaluacion', $idEvaluacion)
            ->where('Id_usuario', $usuario->Id_usuario)
            ->first();

        return view('estudiante.evaluacion_detalle', compact(
            'usuario',
            'evaluacion',
            'entregaExistente',
            'progresoEvaluacion'
        ));

    } catch (\Exception $e) {
        Log::error('ERROR al ver evaluación: ' . $e->getMessage());
        return redirect('/estudiante/dashboard')->with('error', 'Error al cargar la evaluación.');
    }
}

/**
 * ENVIAR ENTREGA DE EVALUACIÓN
 */
public function enviarEntrega(Request $request, $idEvaluacion)
{
    if (!session('usuario')) {
        return redirect('/login');
    }

    $usuario = session('usuario');

    try {
        DB::beginTransaction();

        // Validar datos
        $request->validate([
            'descripcion' => 'required|string|max:1000',
            'archivo' => 'nullable|file|max:10240' // 10MB máximo
        ]);

        // Verificar que no existe entrega previa
        $entregaExistente = DB::table('entregas')
            ->where('Id_evaluacion', $idEvaluacion)
            ->where('Id_usuario', $usuario->Id_usuario)
            ->first();

        if ($entregaExistente) {
            return back()->with('error', 'Ya has enviado una entrega para esta evaluación.');
        }

        // Procesar archivo si se subió
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
            'Descripcion' => $request->descripcion,
            'Archivo' => $nombreArchivo,
            'Fecha_entrega' => now(),
            'Estado' => 'pendiente',
            'Puntos_asignados' => 0
        ]);

        DB::commit();

        Log::info("✅ Entrega enviada - Evaluación: $idEvaluacion, Usuario: {$usuario->Id_usuario}");

        return back()->with('success', '¡Entrega enviada correctamente! El docente la revisará pronto.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('ERROR enviando entrega: ' . $e->getMessage());
        return back()->with('error', 'Error al enviar la entrega: ' . $e->getMessage());
    }
}

/**
 * VER ENTREGAS DEL ESTUDIANTE
 */
public function misEntregas()
{
    if (!session('usuario')) {
        return redirect('/login');
    }

    $usuario = session('usuario');

    try {
        $entregas = DB::select("
            SELECT 
                e.Id_entrega,
                e.Descripcion,
                e.Archivo,
                e.Fecha_entrega,
                e.Puntos_asignados,
                e.Estado,
                e.Comentario_docente,
                ev.Tipo as evaluacion_tipo,
                ev.Puntaje_maximo,
                m.Nombre as modulo_nombre,
                c.Titulo as curso_titulo
            FROM entregas e
            INNER JOIN evaluaciones ev ON e.Id_evaluacion = ev.Id_evaluacion
            INNER JOIN modulos m ON ev.Id_modulo = m.Id_modulo
            INNER JOIN cursos c ON m.Id_curso = c.Id_curso
            WHERE e.Id_usuario = ?
            ORDER BY e.Fecha_entrega DESC
        ", [$usuario->Id_usuario]);

        return view('estudiante.mis_entregas', [
            'usuario' => $usuario,
            'entregas' => $entregas
        ]);

    } catch (\Exception $e) {
        Log::error('ERROR obteniendo entregas: ' . $e->getMessage());
        return redirect('/estudiante/dashboard')->with('error', 'Error al cargar las entregas.');
    }
}
 public function procesarEntrega(Request $request, $idEvaluacion)
{
    if (!session('usuario')) {
        return redirect('/login');
    }

    $usuario = session('usuario');

    try {
        DB::beginTransaction();

        // Validar datos
        $request->validate([
            'descripcion' => 'required|string|max:1000',
            'archivo' => 'nullable|file|max:10240' // 10MB máximo
        ]);

        // Verificar que no existe entrega previa
        $entregaExistente = DB::table('entregas')
            ->where('Id_evaluacion', $idEvaluacion)
            ->where('Id_usuario', $usuario->Id_usuario)
            ->first();

        if ($entregaExistente) {
            return back()->with('error', 'Ya has enviado una entrega para esta evaluación.');
        }

        // Procesar archivo si se subió
        $nombreArchivo = null;
        if ($request->hasFile('archivo')) {
            $archivo = $request->file('archivo');
            $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
            
            // Crear directorio si no existe
            if (!file_exists(public_path('storage/entregas'))) {
                mkdir(public_path('storage/entregas'), 0755, true);
            }
            
            $archivo->move(public_path('storage/entregas'), $nombreArchivo);
        }

        // Crear entrega
        DB::table('entregas')->insert([
            'Id_evaluacion' => $idEvaluacion,
            'Id_usuario' => $usuario->Id_usuario,
            'Descripcion' => $request->descripcion,
            'Archivo' => $nombreArchivo,
            'Fecha_entrega' => now(),
            'Estado' => 'pendiente',
            'Puntos_asignados' => 0
        ]);

        DB::commit();

        Log::info("✅ Entrega procesada - Evaluación: $idEvaluacion, Usuario: {$usuario->Id_usuario}");

        return back()->with('success', '¡Entrega enviada correctamente! El docente la revisará pronto.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('ERROR procesando entrega: ' . $e->getMessage());
        return back()->with('error', 'Error al enviar la entrega: ' . $e->getMessage());
    }
}
// En EstudianteController
// En EstudianteController - método verTema
// En EstudianteController - método verTema ACTUALIZADO
public function verTema($idCurso, $idTema)
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

        // Obtener información del tema CON TODOS LOS CAMPOS NUEVOS
        $tema = DB::selectOne("
            SELECT 
                t.*,
                m.Nombre as modulo_nombre,
                m.Id_modulo,
                c.Titulo as curso_titulo,
                CASE WHEN pt.Completado = 1 THEN 1 ELSE 0 END as completado
            FROM temas t
            INNER JOIN modulos m ON t.Id_modulo = m.Id_modulo
            INNER JOIN cursos c ON m.Id_curso = c.Id_curso
            LEFT JOIN progreso_tema pt ON (t.Id_tema = pt.Id_tema AND pt.Id_usuario = ?)
            WHERE t.Id_tema = ?
        ", [$usuario->Id_usuario, $idTema]);

        if (!$tema) {
            return redirect()->route('estudiante.curso.ver', $idCurso)
                ->with('error', 'Tema no encontrado.');
        }

        // Obtener temas del módulo para navegación
        $temas_modulo = DB::select("
            SELECT 
                t.Id_tema,
                t.Nombre,
                t.Orden,
                CASE WHEN pt.Completado = 1 THEN 1 ELSE 0 END as completado
            FROM temas t
            LEFT JOIN progreso_tema pt ON (t.Id_tema = pt.Id_tema AND pt.Id_usuario = ?)
            WHERE t.Id_modulo = ?
            ORDER BY t.Orden
        ", [$usuario->Id_usuario, $tema->Id_modulo]);

        return view('estudiante.tema_detalle', compact(
            'usuario',
            'tema',
            'temas_modulo',
            'idCurso'
        ));

    } catch (\Exception $e) {
        Log::error('ERROR al ver tema: ' . $e->getMessage());
        return redirect()->route('estudiante.curso.ver', $idCurso)
            ->with('error', 'Error al cargar el tema.');
    }
}
// En EstudianteController - CORREGIR el método completarTema
// En EstudianteController - método completarTema (ACTUALIZAR la sección de porcentaje)
// En EstudianteController - método completarTema ACTUALIZADO
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

        // 1. VERIFICAR SI EL TEMA YA ESTÁ COMPLETADO POR ESTE USUARIO
        $progreso_tema = DB::table('progreso_tema')
            ->where('Id_tema', $idTema)
            ->where('Id_usuario', $usuarioId)
            ->where('Completado', 1)
            ->first();

        if ($progreso_tema) {
            Log::warning("⚠️ Tema $idTema YA estaba completado por usuario $usuarioId");
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Este tema ya estaba completado']);
        }

        Log::info("✅ Tema $idTema no completado, procediendo...");

        // 2. CREAR REGISTRO EN progreso_tema
        DB::table('progreso_tema')->insert([
            'Id_usuario' => $usuarioId,
            'Id_tema' => $idTema,
            'Completado' => 1,
            'Porcentaje' => 100,
            'Fecha_completado' => now()
        ]);

        Log::info("✅ Progreso_tema creado para tema $idTema, usuario $usuarioId");

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

        // 4. ACTUALIZAR ESTADÍSTICAS DEL CURSO - CÁLCULO MEJORADO
        // Calcular total de temas del curso
        $total_temas_curso = DB::selectOne("
            SELECT COUNT(*) as total 
            FROM temas t
            INNER JOIN modulos m ON t.Id_modulo = m.Id_modulo
            WHERE m.Id_curso = ?
        ", [$idCurso])->total;

        $temas_completados_curso = DB::selectOne("
            SELECT COUNT(*) as completados
            FROM progreso_tema pt
            INNER JOIN temas t ON pt.Id_tema = t.Id_tema
            INNER JOIN modulos m ON t.Id_modulo = m.Id_modulo
            WHERE m.Id_curso = ? AND pt.Id_usuario = ? AND pt.Completado = 1
        ", [$idCurso, $usuarioId])->completados;

        $nuevo_porcentaje = $total_temas_curso > 0 ? 
            round(($temas_completados_curso / $total_temas_curso) * 100) : 0;
        $nuevo_porcentaje = min(100, $nuevo_porcentaje);

        Log::info("📈 Progreso actualizado: $temas_completados_curso/$total_temas_curso temas = $nuevo_porcentaje%");

        // Actualizar progreso del curso
        DB::table('progreso_curso')
            ->where('Id_progreso', $progreso->Id_progreso)
            ->update([
                'Temas_completados' => $temas_completados_curso,
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

        // RESPONDER CON JSON PERO SIN REDIRECCIÓN
        return response()->json([
            'success' => true, 
            'message' => '¡Tema completado exitosamente!' . 
                        ($moduloCompletado ? ' + Módulo completado!' : '') .
                        ($cursoCompletado ? ' + Curso completado!' : ''),
            'puntos_otorgados' => 10 + ($moduloCompletado ? 50 : 0) + ($cursoCompletado ? 200 : 0),
            'nuevo_porcentaje' => $nuevo_porcentaje,
            'temas_completados' => $temas_completados_curso,
            'total_temas' => $total_temas_curso,
            'redirect' => false // ← IMPORTANTE: Evitar redirección
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('❌ ERROR en completarTema: ' . $e->getMessage());
        Log::error('Trace: ' . $e->getTraceAsString());
        return response()->json(['success' => false, 'message' => 'Error interno del sistema: ' . $e->getMessage()]);
    }
}
}