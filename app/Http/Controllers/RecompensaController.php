<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecompensaController extends Controller
{
    /**
     * MOSTRAR CATÁLOGO DE RECOMPENSAS
     */
    public function catalogoRecompensas()
    {
        if (!session('usuario')) {
            return redirect('/login');
        }

        $usuario = session('usuario');

        try {
            // Obtener recompensas activas
            $recompensas = DB::table('recompensas')
                ->where('Estado', 'activa')
                ->orderBy('Costo_puntos', 'ASC')
                ->get();

            // Obtener puntos del usuario
            $puntosUsuario = DB::table('gestion_puntos')
                ->where('Id_usuario', $usuario->Id_usuario)
                ->first();

            // Obtener historial de canjes del usuario
            $historialCanjes = DB::select("
                SELECT 
                    c.*,
                    r.Nombre as recompensa_nombre,
                    r.Descripcion as recompensa_descripcion,
                    r.Tipo as recompensa_tipo,
                    CASE 
                        WHEN c.Estado = 1 THEN 'pendiente'
                        WHEN c.Estado = 2 THEN 'aprobado' 
                        WHEN c.Estado = 3 THEN 'rechazado'
                        WHEN c.Estado = 4 THEN 'entregado'
                        ELSE 'desconocido'
                    END as estado_texto
                FROM canjes c
                INNER JOIN recompensas r ON c.Id_recompensa = r.Id_recompensa
                WHERE c.Id_usuario = ?
                ORDER BY c.Fecha_canje DESC
            ", [$usuario->Id_usuario]);

            return view('estudiante.catalogo_recompensas', [
                'usuario' => $usuario,
                'recompensas' => $recompensas,
                'puntosUsuario' => $puntosUsuario,
                'historialCanjes' => $historialCanjes
            ]);

        } catch (\Exception $e) {
            Log::error('ERROR cargando catálogo de recompensas: ' . $e->getMessage());
            return redirect('/estudiante/dashboard')->with('error', 'Error al cargar el catálogo de recompensas.');
        }
    }

    /**
     * PROCESAR CANJE DE RECOMPENSA - CORREGIDO CON ESTADOS NUMÉRICOS
     */
    public function canjearRecompensa(Request $request, $idRecompensa)
    {
        if (!session('usuario')) {
            return redirect('/login');
        }

        $usuario = session('usuario');

        try {
            DB::beginTransaction();

            Log::info("🔄 Iniciando canje - Usuario: {$usuario->Id_usuario}, Recompensa: $idRecompensa");

            // 1. Verificar que la recompensa existe y está activa
            $recompensa = DB::table('recompensas')
                ->where('Id_recompensa', $idRecompensa)
                ->where('Estado', 'activa')
                ->first();

            if (!$recompensa) {
                Log::warning("❌ Recompensa no disponible - ID: $idRecompensa");
                return back()->with('error', 'La recompensa no está disponible.');
            }

            // 2. Verificar stock si aplica
            if ($recompensa->Stock !== null && $recompensa->Stock <= 0) {
                Log::warning("❌ Recompensa agotada - ID: $idRecompensa");
                return back()->with('error', 'Esta recompensa está agotada.');
            }

            // 3. Obtener puntos del usuario
            $puntosUsuario = DB::table('gestion_puntos')
                ->where('Id_usuario', $usuario->Id_usuario)
                ->first();

            $puntosDisponibles = $puntosUsuario ? $puntosUsuario->Total_puntos_actual : 0;
            
            Log::info("💰 Puntos del usuario - Disponibles: $puntosDisponibles, Requeridos: {$recompensa->Costo_puntos}");

            if ($puntosDisponibles < $recompensa->Costo_puntos) {
                Log::warning("❌ Puntos insuficientes - Usuario: {$usuario->Id_usuario}, Disponibles: $puntosDisponibles, Necesarios: {$recompensa->Costo_puntos}");
                return back()->with('error', 
                    "No tienes suficientes puntos para canjear esta recompensa.\n\n" .
                    "💡 Necesitas: {$recompensa->Costo_puntos} puntos\n" .
                    "💰 Tienes: $puntosDisponibles puntos\n" .
                    "📊 Te faltan: " . ($recompensa->Costo_puntos - $puntosDisponibles) . " puntos"
                );
            }

            // 4. Crear el canje (usando Estado numérico: 1 = pendiente)
            $idCanje = DB::table('canjes')->insertGetId([
                'Id_usuario' => $usuario->Id_usuario,
                'Id_recompensa' => $idRecompensa,
                'Puntos_utilizados' => $recompensa->Costo_puntos,
                'Fecha_canje' => now(),
                'Estado' => 1, // 1 = pendiente
                'Comentario' => $request->comentario ?? null
            ]);

            Log::info("✅ Canje creado - ID: $idCanje");

            // 5. Actualizar puntos del usuario
            DB::table('gestion_puntos')
                ->where('Id_usuario', $usuario->Id_usuario)
                ->update([
                    'Total_puntos_actual' => $puntosDisponibles - $recompensa->Costo_puntos,
                    'puntos_canjeados' => ($puntosUsuario->puntos_canjeados ?? 0) + $recompensa->Costo_puntos,
                    
                ]);

            Log::info("💰 Puntos actualizados - Nuevo saldo: " . ($puntosDisponibles - $recompensa->Costo_puntos));

            // 6. Actualizar stock si aplica
            if ($recompensa->Stock !== null) {
                DB::table('recompensas')
                    ->where('Id_recompensa', $idRecompensa)
                    ->update([
                        'Stock' => $recompensa->Stock - 1
                    ]);
                Log::info("📦 Stock actualizado - Nuevo stock: " . ($recompensa->Stock - 1));
            }

            DB::commit();

            Log::info("🎉 Canje completado exitosamente - Usuario: {$usuario->Id_usuario}, Recompensa: {$recompensa->Nombre}, Puntos: {$recompensa->Costo_puntos}");

            return redirect('/estudiante/recompensas')
                ->with('success', 
                    "🎉 ¡Felicidades! Has canjeado '{$recompensa->Nombre}' exitosamente.\n\n" .
                    "💰 Puntos utilizados: {$recompensa->Costo_puntos}\n" .
                    "📊 Tu nuevo saldo: " . ($puntosDisponibles - $recompensa->Costo_puntos) . " puntos\n" .
                    "⏳ Estado: Pendiente de revisión"
                );

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ ERROR canjeando recompensa: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Error al procesar el canje: ' . $e->getMessage());
        }
    }

    /**
     * VER HISTORIAL DE CANJES - CORREGIDO
     */
    public function miHistorialCanjes()
    {
        if (!session('usuario')) {
            return redirect('/login');
        }

        $usuario = session('usuario');

        try {
            $historial = DB::select("
                SELECT 
                    c.Id_canje,
                    c.Fecha_canje,
                    c.Puntos_utilizados,
                    c.Estado,
                    c.Comentario,
                    r.Nombre as recompensa_nombre,
                    r.Descripcion as recompensa_descripcion,
                    r.Tipo as recompensa_tipo,
                    r.Costo_puntos,
                    CASE 
                        WHEN c.Estado = 1 THEN 'pendiente'
                        WHEN c.Estado = 2 THEN 'aprobado' 
                        WHEN c.Estado = 3 THEN 'rechazado'
                        WHEN c.Estado = 4 THEN 'entregado'
                        ELSE 'desconocido'
                    END as estado_texto
                FROM canjes c
                INNER JOIN recompensas r ON c.Id_recompensa = r.Id_recompensa
                WHERE c.Id_usuario = ?
                ORDER BY c.Fecha_canje DESC
            ", [$usuario->Id_usuario]);

            return view('estudiante.historial_canjes', [
                'usuario' => $usuario,
                'historial' => $historial
            ]);

        } catch (\Exception $e) {
            Log::error('ERROR cargando historial de canjes: ' . $e->getMessage());
            return redirect('/estudiante/dashboard')->with('error', 'Error al cargar el historial de canjes.');
        }
    }
}