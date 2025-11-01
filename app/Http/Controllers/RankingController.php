<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GestionPuntos;
use App\Models\Ranking;
use App\Models\Usuario;
use App\Models\Inscripcion;
use App\Models\Curso;
use Illuminate\Support\Facades\DB;

class RankingController extends Controller
{
    public function index()
    {
        if (!session('usuario')) {
            return redirect('/login')->with('error', 'Debes iniciar sesión primero.');
        }

        $usuario = session('usuario');
        $roles = session('user_roles', []);
        $periodo_actual = date('Y-m');

        try {
            // Obtener ranking del período actual
            $ranking_general = $this->obtenerRankingGeneral($periodo_actual);
            
            // Obtener posición del usuario actual
            $mi_posicion = $this->obtenerPosicionUsuario($usuario->Id_usuario, $periodo_actual);
            
            // Obtener top 10
            $top_10 = $this->obtenerTop10($periodo_actual);
            
            // Estadísticas del ranking
            $estadisticas = [
                'total_participantes' => $ranking_general->count(),
                'periodo_actual' => $periodo_actual,
                'puntos_promedio' => $ranking_general->avg('Total_puntos_acumulados') ?? 0,
                'puntos_maximos' => $ranking_general->max('Total_puntos_acumulados') ?? 0
            ];

        } catch (\Exception $e) {
            // En caso de error, usar datos de ejemplo para desarrollo
            $ranking_general = collect();
            $mi_posicion = null;
            $top_10 = collect();
            $estadisticas = [
                'total_participantes' => 0,
                'periodo_actual' => $periodo_actual,
                'puntos_promedio' => 0,
                'puntos_maximos' => 0
            ];
        }

        return view('ranking.index', [
            'usuario' => $usuario,
            'user_roles' => $roles,
            'ranking_general' => $ranking_general,
            'mi_posicion' => $mi_posicion,
            'top_10' => $top_10,
            'estadisticas' => $estadisticas
        ]);
    }

    public function rankingDocente()
    {
        if (!session('usuario')) {
            return redirect('/login');
        }

        $usuario = session('usuario');
        $roles = session('user_roles', []);

        if (!in_array('Docente', $roles)) {
            return redirect('/login')->with('error', 'No tienes acceso a esta área.');
        }

        $periodo_actual = date('Y-m');

        try {
            // Obtener ranking de estudiantes para los cursos del docente
            $ranking_estudiantes = $this->obtenerRankingEstudiantesDocente($usuario->Id_usuario, $periodo_actual);
            
            $estadisticas = [
                'total_estudiantes' => $ranking_estudiantes->count(),
                'periodo_actual' => $periodo_actual,
                'curso_mas_popular' => $this->obtenerCursoMasPopular($usuario->Id_usuario)
            ];

        } catch (\Exception $e) {
            // Datos de ejemplo para desarrollo
            $ranking_estudiantes = $this->obtenerDatosEjemploRanking();
            $estadisticas = [
                'total_estudiantes' => $ranking_estudiantes->count(),
                'periodo_actual' => $periodo_actual,
                'curso_mas_popular' => (object)['Titulo' => 'Matemáticas Básicas']
            ];
        }

        return view('ranking.docente', [
            'usuario' => $usuario,
            'user_roles' => $roles,
            'ranking_estudiantes' => $ranking_estudiantes,
            'estadisticas' => $estadisticas
        ]);
    }

   public function rankingAdmin()
{
    if (!session('usuario')) {
        return redirect('/login');
    }

    $usuario = session('usuario');
    $roles = session('user_roles', []);

    if (!in_array('Administrador', $roles)) {
        return redirect('/login')->with('error', 'No tienes acceso a esta área.');
    }

    $periodo_actual = date('Y-m');

    try {
        $ranking_completo = $this->obtenerRankingCompleto($periodo_actual);
        $estadisticas_sistema = $this->obtenerEstadisticasSistema();

        // Si no hay datos, usar datos de ejemplo
        if ($ranking_completo->count() === 0) {
            $ranking_completo = $this->obtenerDatosEjemploRanking();
        }

    } catch (\Exception $e) {
        // Datos de ejemplo para desarrollo
        $ranking_completo = $this->obtenerDatosEjemploRanking();
        $estadisticas_sistema = $this->obtenerEstadisticasSistemaEjemplo();
    }

    return view('ranking.admin', [
        'usuario' => $usuario,
        'user_roles' => $roles,
        'ranking_completo' => $ranking_completo,
        'estadisticas_sistema' => $estadisticas_sistema,
        'periodo_actual' => $periodo_actual
    ]);
}

    // Métodos auxiliares corregidos
    private function obtenerRankingGeneral($periodo)
    {
        return GestionPuntos::with(['usuario', 'ranking' => function($query) use ($periodo) {
                $query->where('Periodo', $periodo);
            }])
            ->whereHas('ranking', function($query) use ($periodo) {
                $query->where('Periodo', $periodo);
            })
            ->orderBy('Total_puntos_acumulados', 'DESC')
            ->get();
    }

    private function obtenerPosicionUsuario($usuarioId, $periodo)
    {
        $gestion_puntos = GestionPuntos::where('Id_usuario', $usuarioId)->first();
        
        if (!$gestion_puntos || !$gestion_puntos->Id_ranking) {
            return null;
        }

        $ranking = Ranking::find($gestion_puntos->Id_ranking);
        return $ranking ? $ranking->Posicion : null;
    }

    private function obtenerTop10($periodo)
    {
        return GestionPuntos::with(['usuario', 'ranking' => function($query) use ($periodo) {
                $query->where('Periodo', $periodo);
            }])
            ->whereHas('ranking', function($query) use ($periodo) {
                $query->where('Periodo', $periodo);
            })
            ->orderBy('Total_puntos_acumulados', 'DESC')
            ->limit(10)
            ->get();
    }

    private function obtenerRankingEstudiantesDocente($docenteId, $periodo)
    {
        // Para desarrollo, retornar datos de ejemplo
        return $this->obtenerDatosEjemploRanking();
    }

    private function obtenerCursoMasPopular($docenteId)
    {
        return DB::table('cursos')
            ->where('docente_id', $docenteId)
            ->orderBy('inscripciones_count', 'DESC')
            ->first();
    }

    private function obtenerRankingCompleto($periodo)
    {
        return GestionPuntos::with(['usuario', 'ranking' => function($query) use ($periodo) {
                $query->where('Periodo', $periodo);
            }])
            ->whereHas('ranking', function($query) use ($periodo) {
                $query->where('Periodo', $periodo);
            })
            ->orderBy('Total_puntos_acumulados', 'DESC')
            ->get();
    }

    private function obtenerEstadisticasSistema()
    {
        return [
            'total_estudiantes' => DB::table('usuarios')->where('rol', 'Estudiante')->count(),
            'total_docentes' => DB::table('usuarios')->where('rol', 'Docente')->count(),
            'total_cursos' => DB::table('cursos')->count(),
            'total_puntos_distribuidos' => DB::table('gestion_puntos')->sum('Total_puntos_acumulados'),
            'total_canjes' => DB::table('canjes')->count()
        ];
    }

    // Métodos para datos de ejemplo (desarrollo)
    private function obtenerDatosEjemploRanking()
{
    // Datos de ejemplo más completos para desarrollo
    return collect([
        (object)[
            'Id_usuario' => 1,
            'Total_puntos_acumulados' => 1500,
            'Total_puntos_actual' => 1200,
            'usuario' => (object)[
                'Id_usuario' => 1,
                'Nombre' => 'Ana',
                'Apellido' => 'García',
                'Email' => 'ana@ejemplo.com',
                'Estado' => true,
                'progresos' => collect([(object)['Porcentaje' => 85], (object)['Porcentaje' => 90]]),
                'canjes' => collect([(object)[], (object)[]]) // 2 canjes
            ]
        ],
        (object)[
            'Id_usuario' => 2,
            'Total_puntos_acumulados' => 1200,
            'Total_puntos_actual' => 900,
            'usuario' => (object)[
                'Id_usuario' => 2,
                'Nombre' => 'Carlos',
                'Apellido' => 'López',
                'Email' => 'carlos@ejemplo.com',
                'Estado' => true,
                'progresos' => collect([(object)['Porcentaje' => 75], (object)['Porcentaje' => 80]]),
                'canjes' => collect([(object)[]]) // 1 canje
            ]
        ],
        (object)[
            'Id_usuario' => 3,
            'Total_puntos_acumulados' => 800,
            'Total_puntos_actual' => 600,
            'usuario' => (object)[
                'Id_usuario' => 3,
                'Nombre' => 'María',
                'Apellido' => 'Rodríguez',
                'Email' => 'maria@ejemplo.com',
                'Estado' => true,
                'progresos' => collect([(object)['Porcentaje' => 60], (object)['Porcentaje' => 70]]),
                'canjes' => collect([]) // 0 canjes
            ]
        ],
        (object)[
            'Id_usuario' => 4,
            'Total_puntos_acumulados' => 600,
            'Total_puntos_actual' => 450,
            'usuario' => (object)[
                'Id_usuario' => 4,
                'Nombre' => 'Juan',
                'Apellido' => 'Martínez',
                'Email' => 'juan@ejemplo.com',
                'Estado' => true,
                'progresos' => collect([(object)['Porcentaje' => 45], (object)['Porcentaje' => 55]]),
                'canjes' => collect([(object)[]]) // 1 canje
            ]
        ],
        (object)[
            'Id_usuario' => 5,
            'Total_puntos_acumulados' => 400,
            'Total_puntos_actual' => 300,
            'usuario' => (object)[
                'Id_usuario' => 5,
                'Nombre' => 'Laura',
                'Apellido' => 'Hernández',
                'Email' => 'laura@ejemplo.com',
                'Estado' => true,
                'progresos' => collect([(object)['Porcentaje' => 30], (object)['Porcentaje' => 40]]),
                'canjes' => collect([]) // 0 canjes
            ]
        ]
    ]);
}    private function obtenerEstadisticasSistemaEjemplo()
    {
        return [
            'total_estudiantes' => 50,
            'total_docentes' => 5,
            'total_cursos' => 12,
            'total_puntos_distribuidos' => 45000,
            'total_canjes' => 23
        ];
    }

    // API para obtener ranking (AJAX)
    public function obtenerRankingJson($periodo = null)
    {
        $periodo = $periodo ?? date('Y-m');
        
        try {
            $ranking = $this->obtenerTop10($periodo);
            
            return response()->json([
                'success' => true,
                'periodo' => $periodo,
                'ranking' => $ranking,
                'fecha_actualizacion' => now()->format('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}