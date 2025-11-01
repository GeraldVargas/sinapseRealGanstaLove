<?php

namespace App\Http\Controllers;

use App\Models\Inscripcion;
use App\Models\ProgresoCurso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InscripcionController extends Controller
{
    public function inscribirCurso(Request $request)
    {
        // Verificar autenticación
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Debe iniciar sesión para inscribirse en cursos'
            ], 401);
        }

        try {
            $userId = Auth::id();

            // Validar datos
            $request->validate([
                'curso_id' => 'required|exists:cursos,Id_curso',
            ]);

            // Verificar si ya está inscrito
            $inscripcionExistente = Inscripcion::where('Id_usuario', $userId)
                ->where('Id_curso', $request->curso_id)
                ->first();

            if ($inscripcionExistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya estás inscrito en este curso'
                ], 400);
            }

            // Esta inserción disparará el trigger before_inscripcion_validar_cupos
            // y after_inscripcion_bitacora automáticamente
            $inscripcion = Inscripcion::create([
                'Id_usuario' => $userId,
                'Id_curso' => $request->curso_id,
                'Fecha_inscripcion' => now()->format('Y-m-d'),
                'Estado' => false, // Pendiente de pago
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Inscripción realizada correctamente',
                'inscripcion_id' => $inscripcion->Id_inscripcion
            ]);
            
        } catch (\Exception $e) {
            // Capturar error del trigger de validación de cupos
            if (strpos($e->getMessage(), 'cupos disponibles') !== false) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error en la inscripción: ' . $e->getMessage()
            ], 500);
        }
    }

    public function misInscripciones()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userId = Auth::id();
        $inscripciones = Inscripcion::with('curso')
            ->where('Id_usuario', $userId)
            ->orderBy('Fecha_inscripcion', 'desc')
            ->get();

        return view('inscripciones.mis_inscripciones', compact('inscripciones'));
    }

    // ⚠️ NO AGREGUES MÉTODOS CON $this->post() - eso es solo para tests ⚠️
}