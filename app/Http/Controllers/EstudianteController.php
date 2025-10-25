<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Curso;
use App\Models\ProgresoCurso;
use App\Models\Insignia;
use App\Models\Recompensa;
use App\Services\PuntoService;

class EstudianteController extends Controller
{
    protected $puntoService;

    public function __construct(PuntoService $puntoService)
    {
        $this->puntoService = $puntoService;
    }

    public function dashboard()
    {
        if (!session('usuario')) {
            return redirect('/login')->with('error', 'Debes iniciar sesión primero.');
        }

        $usuario = session('usuario');
        $roles = session('user_roles', []);

        if (!in_array('Estudiante', $roles)) {
            return redirect('/login')->with('error', 'No tienes acceso a esta área.');
        }

        try {
            // Obtener cursos del estudiante
            $cursos_inscritos = Curso::limit(4)->get();
            
            // Obtener progresos del estudiante
            $progresos = ProgresoCurso::where('id_Usuario', $usuario->Id_usuario)->get();
            
            // Obtener insignias del estudiante
            $insignias = $usuario->insignias;
            
            // Obtener recompensas disponibles
            $recompensas = Recompensa::where('estado', 1)->get();
            
            // Calcular puntos totales usando el nuevo método
            $puntos_totales = $usuario->puntos_totales;

            // Obtener historial de puntos (últimos 5)
            $historial_puntos = $usuario->puntos()
                ->orderBy('fecha', 'desc')
                ->limit(5)
                ->get();

        } catch (\Exception $e) {
            $cursos_inscritos = collect();
            $progresos = collect();
            $insignias = collect();
            $recompensas = collect();
            $puntos_totales = 0;
            $historial_puntos = collect();
        }

        return view('estudiante.dashboard', [
            'usuario' => $usuario,
            'user_roles' => $roles,
            'cursos_inscritos' => $cursos_inscritos,
            'progresos' => $progresos,
            'insignias' => $insignias,
            'recompensas' => $recompensas,
            'puntos_totales' => $puntos_totales,
            'historial_puntos' => $historial_puntos
        ]);
    }

    public function canjearRecompensa(Request $request, $idRecompensa)
    {
        if (!session('usuario')) {
            return redirect('/login');
        }

        $usuario = session('usuario');
        $recompensa = Recompensa::find($idRecompensa);

        if (!$recompensa) {
            return back()->with('error', 'Recompensa no encontrada.');
        }

        if ($this->puntoService->canjearRecompensa($usuario, $recompensa)) {
            return back()->with('success', '¡Recompensa canjeada exitosamente!');
        } else {
            return back()->with('error', 'No tienes suficientes puntos para canjear esta recompensa.');
        }
    }
}