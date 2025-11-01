<?php

namespace App\Http\Controllers;

use App\Models\Canje;
use App\Models\GestionPuntos;
use App\Models\Recompensa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CanjeController extends Controller
{
    public function index()
    {
        // Verificar autenticación
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debe iniciar sesión para acceder a esta página.');
        }

        $recompensas = Recompensa::where('Estado', true)->get();
        $puntosUsuario = GestionPuntos::where('Id_usuario', Auth::id())->first();
        
        return view('canjes.index', compact('recompensas', 'puntosUsuario'));
    }

    public function canjearRecompensa(Request $request)
    {
        // Verificar autenticación
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Debe iniciar sesión para realizar canjes'
            ], 401);
        }

        try {
            $userId = Auth::id();
            
            // Validar datos de entrada
            $request->validate([
                'recompensa_id' => 'required|exists:recompensas,Id_recompe',
            ]);

            // Obtener información de la recompensa
            $recompensa = Recompensa::findOrFail($request->recompensa_id);
            
            // Verificar si la recompensa está disponible
            if (!$recompensa->Estado) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta recompensa no está disponible'
                ], 400);
            }

            // Verificar puntos del usuario
            $puntosUsuario = GestionPuntos::where('Id_usuario', $userId)->first();
            
            if (!$puntosUsuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes puntos disponibles'
                ], 400);
            }

            if ($puntosUsuario->Total_puntos_actual < $recompensa->Costo_puntos) {
                return response()->json([
                    'success' => false,
                    'message' => 'Puntos insuficientes. Necesitas: ' . $recompensa->Costo_puntos . ' puntos. Tienes: ' . $puntosUsuario->Total_puntos_actual . ' puntos'
                ], 400);
            }

            // Crear el canje - esto disparará el trigger after_canje_puntos automáticamente
            $canje = Canje::create([
                'Id_usuario' => $userId,
                'Id_recompensa' => $request->recompensa_id,
                'Fecha_canje' => now(),
                'Estado' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => '¡Recompensa canjeada exitosamente!',
                'canje_id' => $canje->Id_canje,
                'puntos_restantes' => $puntosUsuario->Total_puntos_actual - $recompensa->Costo_puntos
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al canjear recompensa: ' . $e->getMessage()
            ], 500);
        }
    }

    public function misCanjes()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userId = Auth::id();
        $canjes = Canje::with('recompensa')
                      ->where('Id_usuario', $userId)
                      ->orderBy('Fecha_canje', 'desc')
                      ->get();
        
        return view('canjes.mis_canjes', compact('canjes'));
    }

    public function verificarPuntos()
    {
        if (!Auth::check()) {
            return response()->json([
                'puntos_actual' => 0,
                'puntos_acumulados' => 0
            ]);
        }

        $userId = Auth::id();
        $puntosUsuario = GestionPuntos::where('Id_usuario', $userId)->first();
        
        return response()->json([
            'puntos_actual' => $puntosUsuario ? $puntosUsuario->Total_puntos_actual : 0,
            'puntos_acumulados' => $puntosUsuario ? $puntosUsuario->Total_puntos_acumulados : 0
        ]);
    }
    // En CanjeController - método de prueba
public function testTriggerCanje()
{
    $usuario = Auth::user();
    $puntosIniciales = GestionPuntos::where('Id_usuario', $usuario->id)->first()->Total_puntos_actual;
    
    // Realizar canje
    $canje = Canje::create([
        'Id_usuario' => $usuario->id,
        'Id_recompensa' => 1, // Recompensa de 50 puntos
        'Fecha_canje' => now(),
        'Estado' => true
    ]);

    $puntosFinales = GestionPuntos::where('Id_usuario', $usuario->id)->first()->Total_puntos_actual;
    
    // Debería haber disminuido en 50 puntos
    return $puntosIniciales - $puntosFinales; // Debería ser 50
}
}