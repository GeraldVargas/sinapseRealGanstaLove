<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Curso;
use App\Models\Insignia;
use App\Models\Usuario;

class EstudianteController extends Controller
{
    public function index()
    {
        try {
            // Obtener cursos REALES
            $cursos_inscritos = Curso::limit(4)->get();
            
            // Obtener insignias REALES
            $insignias = Insignia::limit(6)->get();
            
            // Calcular progresos REALES (usando el primer usuario como ejemplo)
            $usuario_ejemplo = Usuario::first();
            $puntos_totales = $usuario_ejemplo ? $usuario_ejemplo->insignias->sum('Valor_Puntos') : 0;
            $insignias_obtenidas = $usuario_ejemplo ? $usuario_ejemplo->insignias->count() : 0;

        } catch (\Exception $e) {
            $cursos_inscritos = collect();
            $insignias = collect();
            $puntos_totales = 0;
            $insignias_obtenidas = 0;
        }

        $progresos = [
            'cursos_completados' => 3, // Por ahora estático
            'cursos_en_progreso' => 2, // Por ahora estático
            'puntos_totales' => $puntos_totales,
            'insignias_obtenidas' => $insignias_obtenidas
        ];

        return view('estudiante.dashboard', compact('cursos_inscritos', 'progresos', 'insignias'));
    }
}