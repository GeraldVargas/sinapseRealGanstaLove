<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TriggerMonitorController extends Controller
{
    public function index()
    {
        $triggers = [
            [
                'nombre' => 'Validación de Cupos',
                'descripcion' => 'Valida disponibilidad antes de inscripción',
                'tabla' => 'inscripciones',
                'estado' => $this->verificarTrigger('before_inscripcion_validar_cupos')
            ],
            [
                'nombre' => 'Puntos por Evaluación',
                'descripcion' => 'Asigna puntos al aprobar evaluación',
                'tabla' => 'progreso_evaluacion',
                'estado' => $this->verificarTrigger('after_evaluacion_aprobada')
            ],
            [
                'nombre' => 'Actualización de Ranking',
                'descripcion' => 'Actualiza ranking al cambiar puntos',
                'tabla' => 'gestion_puntos',
                'estado' => $this->verificarTrigger('after_actualizar_puntos_ranking')
            ],
            [
                'nombre' => 'Canje de Puntos',
                'descripcion' => 'Actualiza saldo después de canje',
                'tabla' => 'canjes',
                'estado' => $this->verificarTrigger('after_canje_puntos')
            ],
            [
                'nombre' => 'Bitácora de Inscripciones',
                'descripcion' => 'Registro automático en bitácora',
                'tabla' => 'inscripciones',
                'estado' => $this->verificarTrigger('after_inscripcion_bitacora')
            ],
            [
                'nombre' => 'Bitácora de Pagos',
                'descripcion' => 'Registro automático de pagos',
                'tabla' => 'pago',
                'estado' => $this->verificarTrigger('after_pago_bitacora')
            ]
        ];

        // Obtener estadísticas de bitácoras
        $totalInscripciones = 0;
        $ultimaInscripcion = null;
        $totalPagos = 0;
        $ultimoPago = null;

        try {
            $totalInscripciones = DB::table('bitacora_inscripciones')->count();
            $ultimaInscripcion = DB::table('bitacora_inscripciones')
                ->orderBy('Fecha_registro', 'desc')
                ->first();

            $totalPagos = DB::table('bitacora_pagos')->count();
            $ultimoPago = DB::table('bitacora_pagos')
                ->orderBy('Fecha_registro', 'desc')
                ->first();
        } catch (\Exception $e) {
            // Las tablas de bitácora pueden no existir aún
        }

        return view('admin.triggers', compact(
            'triggers', 
            'totalInscripciones', 
            'ultimaInscripcion',
            'totalPagos',
            'ultimoPago'
        ));
    }

    private function verificarTrigger($triggerName)
    {
        try {
            $triggers = DB::select("SHOW TRIGGERS");
            
            foreach ($triggers as $trigger) {
                if ($trigger->Trigger == $triggerName) {
                    return true;
                }
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function probarTriggerCupos(Request $request)
    {
        try {
            // Buscar un curso que exista
            $curso = DB::table('cursos')->first();
            
            if (!$curso) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay cursos para probar'
                ]);
            }

            // Crear un usuario de prueba temporal
            $usuarioTestId = DB::table('usuarios')->insertGetId([
                'Nombre' => 'Usuario',
                'Apellido' => 'Test Trigger',
                'Email' => 'test_trigger_' . time() . '@test.com',
                'Contrasena' => bcrypt('password'),
                'Fecha_registro' => now(),
                'Estado' => true
            ]);

            // Intentar insertar múltiples inscripciones para llenar cupos
            for ($i = 0; $i < 60; $i++) {
                DB::table('inscripciones')->insert([
                    'Id_usuario' => $usuarioTestId + $i,
                    'Id_curso' => $curso->Id_curso,
                    'Fecha_inscripcion' => now(),
                    'Estado' => true
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Trigger de cupos NO funcionó correctamente (debería haber dado error)'
            ]);

        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'cupos disponibles') !== false) {
                return response()->json([
                    'success' => true,
                    'message' => '✅ Trigger de cupos funcionando correctamente'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function probarTriggerPuntos(Request $request)
    {
        try {
            // Crear datos de prueba mínimos
            $usuario = DB::table('usuarios')->first();
            $curso = DB::table('cursos')->first();
            
            if (!$usuario || !$curso) {
                return response()->json([
                    'success' => false,
                    'message' => 'Necesitas tener usuarios y cursos para probar'
                ]);
            }

            // Crear progreso de curso primero
            $progresoCursoId = DB::table('progreso_curso')->insertGetId([
                'Id_usuario' => $usuario->Id_usuario,
                'Id_curso' => $curso->Id_curso,
                'Fecha_actualizacion' => now(),
                'Porcentaje' => 0,
                'Nivel' => 1,
                'Modulos_completados' => 0,
                'Temas_completados' => 0,
                'Evaluaciones_superadas' => 0,
                'Actividades_superadas' => 0
            ]);

            // Crear progreso de evaluación
            $progresoEvaluacionId = DB::table('progreso_evaluacion')->insertGetId([
                'Id_progreso_evaluacion' => $progresoCursoId, // Relacionado con progreso_curso
                'Id_evaluacion' => 1,
                'Puntaje_obtenido' => 70,
                'Porcentaje' => 70,
                'Aprobado' => false,
                'Fecha_completado' => null
            ]);

            // Obtener puntos antes
            $puntosAntes = DB::table('gestion_puntos')
                ->where('Id_usuario', $usuario->Id_usuario)
                ->value('Total_puntos_actual') ?? 0;

            // Actualizar a aprobado - debería disparar el trigger
            DB::table('progreso_evaluacion')
                ->where('Id_progreso_evaluacion', $progresoEvaluacionId)
                ->update(['Aprobado' => true]);

            // Obtener puntos después
            $puntosDespues = DB::table('gestion_puntos')
                ->where('Id_usuario', $usuario->Id_usuario)
                ->value('Total_puntos_actual') ?? 0;

            $diferencia = $puntosDespues - $puntosAntes;

            return response()->json([
                'success' => true,
                'message' => "✅ Trigger de puntos probado. Diferencia: {$diferencia} puntos"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function probarTriggerCanje(Request $request)
    {
        try {
            $usuario = DB::table('usuarios')->first();
            $recompensa = DB::table('recompensas')->first();
            
            if (!$usuario || !$recompensa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Necesitas tener usuarios y recompensas para probar'
                ]);
            }

            // Asegurarse de que el usuario tenga puntos
            DB::table('gestion_puntos')->updateOrInsert(
                ['Id_usuario' => $usuario->Id_usuario],
                [
                    'Total_puntos_actual' => 1000,
                    'Total_puntos_acumulados' => 1000,
                    'Total_saldo_usado' => 0
                ]
            );

            $puntosAntes = DB::table('gestion_puntos')
                ->where('Id_usuario', $usuario->Id_usuario)
                ->value('Total_puntos_actual');

            // Realizar canje - debería disparar el trigger
            DB::table('canjes')->insert([
                'Id_usuario' => $usuario->Id_usuario,
                'Id_recompensa' => $recompensa->Id_recompe,
                'Fecha_canje' => now(),
                'Estado' => true
            ]);

            $puntosDespues = DB::table('gestion_puntos')
                ->where('Id_usuario', $usuario->Id_usuario)
                ->value('Total_puntos_actual');

            $diferencia = $puntosAntes - $puntosDespues;

            return response()->json([
                'success' => true,
                'message' => "✅ Trigger de canje probado. Diferencia: {$diferencia} puntos"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
}