<?php

namespace App\Http\Controllers;

use App\Models\ProgresoEvaluacion;
use Illuminate\Http\Request;
use App\Models\GestionPuntos;
class EvaluacionController extends Controller
{
    public function calificarEvaluacion(Request $request, $idEvaluacion)
    {
        try {
            $progreso = ProgresoEvaluacion::findOrFail($idEvaluacion);
            
            // Actualizar progreso - esto disparará el trigger after_evaluacion_aprobada
            $progreso->update([
                'Puntaje_obtenido' => $request->puntaje,
                'Porcentaje' => $request->porcentaje,
                'Aprobado' => $request->puntaje >= 70, // 70% para aprobar
                'Fecha_completado' => now(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Evaluación calificada correctamente',
                'aprobado' => $progreso->Aprobado
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al calificar evaluación: ' . $e->getMessage()
            ], 500);
        }
    }
    // En EvaluacionController
public function testTriggerEvaluacion()
{
    // Crear una evaluación no aprobada
    $progreso = ProgresoEvaluacion::create([
        'Id_evaluacion' => 1,
        'Puntaje_obtenido' => 60,
        'Porcentaje' => 60,
        'Aprobado' => false,
        'Fecha_completado' => null
    ]);

    // Actualizar a aprobado - debería disparar el trigger
    $progreso->update([
        'Aprobado' => true,
        'Puntaje_obtenido' => 80,
        'Porcentaje' => 80,
        'Fecha_completado' => now()
    ]);

    // Verificar si se asignaron puntos
    $puntos = GestionPuntos::where('Id_usuario', $progreso->progresoCurso->Id_usuario)->first();
    return $puntos->Total_puntos_actual; // Debería haber aumentado en 20 puntos
}
}