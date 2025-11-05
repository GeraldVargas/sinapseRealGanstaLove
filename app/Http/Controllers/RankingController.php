<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RankingController extends Controller
{
public function index()
{
    if (!session('usuario')) {
        return redirect('/login')->with('error', 'Debes iniciar sesión primero.');
    }

    $usuario = session('usuario');
    $roles = session('user_roles', []);
    
    $periodo_actual = "Octubre 2025";

    try {
        // SINCRONIZAR RANKING CON GESTION_PUNTOS ANTES DE MOSTRAR
        $this->sincronizarRankingConGestionPuntos();
        
        // Obtener ranking del período actual
        $ranking_completo = $this->obtenerRankingEstudiantes($periodo_actual);
        
        // Resto del código igual...
        $miPosicion = $this->obtenerPosicionUsuario($usuario->Id_usuario, $periodo_actual);
        $total_participantes = count($ranking_completo);
        $top_10 = array_slice($ranking_completo, 0, 10);
        $estadisticas = $this->calcularEstadisticas($ranking_completo, $periodo_actual);

    } catch (\Exception $e) {
        Log::error('ERROR en ranking: ' . $e->getMessage());
        $ranking_completo = [];
        $top_10 = [];
        $miPosicion = null;
        $total_participantes = 0;
        $estadisticas = $this->calcularEstadisticas([], $periodo_actual);
    }

    return view('ranking.index', [
        'usuario' => $usuario,
        'user_roles' => $roles,
        'ranking_completo' => $ranking_completo,
        'top_10' => $top_10,
        'miPosicion' => $miPosicion,
        'total_participantes' => $total_participantes,
        'periodo' => $periodo_actual,
        'estadisticas' => $estadisticas
    ]);
}

    /**
     * MÉTODO PARA ACTUALIZAR PUNTOS MANUALMENTE (si es necesario)
     */
    public function actualizarPuntos(Request $request)
    {
        try {
            $usuarioId = $request->input('usuario_id');
            $nuevosPuntos = $request->input('puntos');
            
            // Actualizar gestion_puntos - el trigger se encargará del ranking
            DB::table('gestion_puntos')
                ->where('Id_usuario', $usuarioId)
                ->update([
                    'Total_puntos_actual' => $nuevosPuntos,
                    'Total_puntos_acumulados' => $nuevosPuntos,
                    'puntos_acumulados_total' => $nuevosPuntos
                ]);
                
            return response()->json([
                'success' => true,
                'message' => 'Puntos actualizados correctamente'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Los demás métodos se mantienen igual...
   private function obtenerRankingEstudiantes($periodo)
{
    try {
        Log::info("🔍 Obteniendo ranking para período: $periodo");
        
        $ranking = DB::table('ranking as r')
            ->join('usuarios as u', 'r.Id_usuario', '=', 'u.Id_usuario')
            ->join('rol_usuario as ru', 'u.Id_usuario', '=', 'ru.Id_usuario')
            ->join('roles as rol', 'ru.Id_rol', '=', 'rol.Id_rol')
            ->where('r.Periodo', $periodo)
            ->where('rol.Nombre', 'Estudiante')
            ->where('u.Estado', 1)
            ->select(
                'r.Id_ranking',
                'r.Posicion',
                'r.Periodo',
                'r.Id_usuario',
                'r.Total_puntos_acumulados',
                'r.periodo_date',
                'u.Nombre',
                'u.Apellido',
                'u.Email'
            )
            ->orderBy('r.Posicion', 'ASC')
            ->get();

        Log::info("📊 Estudiantes encontrados en ranking: " . $ranking->count());

        // DEBUG: Ver los primeros 5 registros
        if ($ranking->count() > 0) {
            Log::info("👥 Primeros 5 en ranking:");
            foreach ($ranking->take(5) as $index => $item) {
                Log::info("   {$item->Posicion}. {$item->Nombre} {$item->Apellido} - {$item->Total_puntos_acumulados} pts");
            }
        } else {
            Log::warning("⚠️ No se encontraron estudiantes en el ranking");
            
            // DEBUG: Ver qué hay en la tabla ranking
            $total_ranking = DB::table('ranking')->where('Periodo', $periodo)->count();
            Log::info("📋 Total registros en tabla ranking: $total_ranking");
            
            // DEBUG: Ver estudiantes en rol_usuario
            $total_estudiantes = DB::table('usuarios as u')
                ->join('rol_usuario as ru', 'u.Id_usuario', '=', 'ru.Id_usuario')
                ->join('roles as r', 'ru.Id_rol', '=', 'r.Id_rol')
                ->where('r.Nombre', 'Estudiante')
                ->where('u.Estado', 1)
                ->count();
            Log::info("🎓 Total estudiantes en sistema: $total_estudiantes");
        }

        return $ranking->toArray();

    } catch (\Exception $e) {
        Log::error('❌ ERROR en obtenerRankingEstudiantes: ' . $e->getMessage());
        return [];
    }
}

    private function obtenerPosicionUsuario($usuarioId, $periodo)
    {
        return DB::table('ranking as r')
            ->join('usuarios as u', 'r.Id_usuario', '=', 'u.Id_usuario')
            ->where('r.Id_usuario', $usuarioId)
            ->where('r.Periodo', $periodo)
            ->select(
                'r.Id_ranking',
                'r.Posicion',
                'r.Periodo',
                'r.Id_usuario',
                'r.Total_puntos_acumulados',
                'r.periodo_date',
                'u.Nombre',
                'u.Apellido',
                'u.Email'
            )
            ->first();
    }

    private function calcularEstadisticas($ranking, $periodo)
    {
        if (empty($ranking)) {
            return [
                'total_participantes' => 0,
                'puntos_promedio' => 0,
                'puntos_maximos' => 0,
                'puntos_minimos' => 0,
                'periodo_actual' => $periodo
            ];
        }

        $puntos = array_column($ranking, 'Total_puntos_acumulados');
        
        return [
            'total_participantes' => count($ranking),
            'puntos_promedio' => round(array_sum($puntos) / count($puntos)),
            'puntos_maximos' => max($puntos),
            'puntos_minimos' => min($puntos),
            'periodo_actual' => $periodo
        ];
    }
    /**
 * ACTUALIZAR RANKING COMPLETO DESDE EL CONTROLADOR
 */
public function actualizarRankingCompleto()
{
    try {
        DB::beginTransaction();

        $periodo_actual = "Octubre 2025";
        $periodo_date = '2025-10-01';

        // 1. Limpiar ranking actual
        DB::table('ranking')->where('Periodo', $periodo_actual)->delete();

        // 2. Insertar todos los estudiantes en el ranking
        $estudiantes = DB::select("
            SELECT 
                gp.Id_usuario,
                gp.Total_puntos_acumulados
            FROM gestion_puntos gp
            INNER JOIN usuarios u ON gp.Id_usuario = u.Id_usuario
            INNER JOIN rol_usuario ru ON u.Id_usuario = ru.Id_usuario
            INNER JOIN roles r ON ru.Id_rol = r.Id_rol
            WHERE r.Nombre = 'Estudiante'
            AND u.Estado = 1
        ");

        foreach ($estudiantes as $estudiante) {
            DB::table('ranking')->insert([
                'Posicion' => 0,
                'Periodo' => $periodo_actual,
                'Id_usuario' => $estudiante->Id_usuario,
                'Total_puntos_acumulados' => $estudiante->Total_puntos_acumulados,
                'periodo_date' => $periodo_date,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // 3. Recalcular posiciones
        $this->recalcularPosicionesRankingCompleto($periodo_actual);

        // 4. Actualizar relaciones
        DB::update("
            UPDATE gestion_puntos gp
            INNER JOIN ranking r ON gp.Id_usuario = r.Id_usuario AND r.Periodo = ?
            SET gp.Id_ranking = r.Id_ranking
        ", [$periodo_actual]);

        DB::commit();

        $total_estudiantes = count($estudiantes);
        Log::info("✅ Ranking actualizado completamente: $total_estudiantes estudiantes");

        return response()->json([
            'success' => true,
            'message' => "Ranking actualizado con $total_estudiantes estudiantes",
            'total_estudiantes' => $total_estudiantes
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('❌ ERROR actualizando ranking completo: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}

/**
 * RECALCULAR POSICIONES DEL RANKING COMPLETO
 */
private function recalcularPosicionesRankingCompleto($periodo)
{
    try {
        // Obtener rankings ordenados por puntos
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

        Log::info("✅ Posiciones recalculadas: $periodo - " . ($posicion-1) . " participantes");

    } catch (\Exception $e) {
        Log::error('❌ ERROR recalculando posiciones: ' . $e->getMessage());
    }
}
/**
 * SINCRONIZAR RANKING CON GESTION_PUNTOS
 */
/**
 * SINCRONIZAR RANKING CON GESTION_PUNTOS
 */
private function sincronizarRankingConGestionPuntos()
{
    try {
        $periodo_actual = "Octubre 2025";
        
        Log::info("🔄 Sincronizando ranking con gestion_puntos");

        // 1. Actualizar ranking existente con puntos de gestion_puntos
        $actualizados = DB::update("
            UPDATE ranking r
            INNER JOIN gestion_puntos gp ON r.Id_usuario = gp.Id_usuario
            SET r.Total_puntos_acumulados = gp.Total_puntos_acumulados
            WHERE r.Periodo = ? 
            AND r.Total_puntos_acumulados != gp.Total_puntos_acumulados
        ", [$periodo_actual]);

        Log::info("✅ Registros actualizados en ranking: $actualizados");

        // 2. Insertar estudiantes que no están en ranking pero sí en gestion_puntos
        $insertados = DB::insert("
            INSERT INTO ranking (Posicion, Periodo, Id_usuario, Total_puntos_acumulados, periodo_date)
            SELECT 
                0 as Posicion,
                ? as Periodo,
                gp.Id_usuario,
                gp.Total_puntos_acumulados,
                '2025-10-01' as periodo_date
            FROM gestion_puntos gp
            INNER JOIN usuarios u ON gp.Id_usuario = u.Id_usuario
            INNER JOIN rol_usuario ru ON u.Id_usuario = ru.Id_usuario
            INNER JOIN roles r ON ru.Id_rol = r.Id_rol
            WHERE r.Nombre = 'Estudiante'
            AND u.Estado = 1
            AND gp.Id_usuario NOT IN (
                SELECT Id_usuario FROM ranking WHERE Periodo = ?
            )
        ", [$periodo_actual, $periodo_actual]);

        Log::info("✅ Nuevos registros insertados en ranking: $insertados");

        // 3. Recalcular posiciones SOLO si hubo cambios
        if ($actualizados > 0 || $insertados > 0) {
            $this->recalcularPosicionesRankingSimple($periodo_actual);
        }

    } catch (\Exception $e) {
        Log::error('❌ ERROR sincronizando ranking: ' . $e->getMessage());
    }
}
/**
 * RECALCULAR POSICIONES SIMPLE
 */
private function recalcularPosicionesRankingSimple($periodo)
{
    try {
        Log::info("🔄 Recalculando posiciones para: $periodo");

        // Obtener rankings ordenados por puntos
        $rankings = DB::select("
            SELECT Id_ranking 
            FROM ranking 
            WHERE Periodo = ?
            ORDER BY Total_puntos_acumulados DESC
        ", [$periodo]);

        // Actualizar posiciones
        $posicion = 1;
        foreach ($rankings as $ranking) {
            DB::table('ranking')
                ->where('Id_ranking', $ranking->Id_ranking)
                ->update(['Posicion' => $posicion]);
            $posicion++;
        }

        Log::info("✅ Posiciones recalculadas: $periodo - " . ($posicion-1) . " participantes");

    } catch (\Exception $e) {
        Log::error('❌ ERROR recalculando posiciones: ' . $e->getMessage());
    }
}
}