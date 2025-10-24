<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Curso;

class DocenteController extends Controller
{
    public function index()
    {
        try {
            $mis_cursos = Curso::limit(4)->get();
        } catch (\Exception $e) {
            $mis_cursos = collect();
        }

        $estadisticas_docente = [
            'cursos_impartidos' => 6,
            'estudiantes_total' => 85,
            'evaluaciones_pendientes' => 12,
            'rating_promedio' => 4.8
        ];

        return view('docente.dashboard', compact('mis_cursos', 'estadisticas_docente'));
    }
}