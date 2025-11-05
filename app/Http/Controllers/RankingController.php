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
        
        // Usar el período exacto de la base de datos
        $periodo_actual = "Octubre 2025";

        try {
            // Obtener ranking del período actual
            $ranking = $this->obtenerRankingCompleto($periodo_actual);
            
            // Marcar usuario actual en el ranking
            $ranking = $this->marcarUsuarioActual($ranking, $usuario->Id_usuario);
            
            // Obtener posición del usuario actual
            $miPosicion = $this->obtenerPosicionUsuario($usuario->Id_usuario, $periodo_actual);
            
            $total_participantes = count($ranking);

            // Si no hay datos en la BD, usar los de ejemplo
            if ($total_participantes === 0) {
                $ranking = $this->obtenerDatosEjemploRanking();
                $ranking = $this->marcarUsuarioActual($ranking, $usuario->Id_usuario);
                $miPosicion = $this->obtenerMiPosicionEjemplo($usuario->Id_usuario);
                $total_participantes = count($ranking);
            }

        } catch (\Exception $e) {
            // En caso de error, usar datos de ejemplo
            $ranking = $this->obtenerDatosEjemploRanking();
            $ranking = $this->marcarUsuarioActual($ranking, $usuario->Id_usuario);
            $miPosicion = $this->obtenerMiPosicionEjemplo($usuario->Id_usuario);
            $total_participantes = count($ranking);
        }

        return view('ranking.index', [
            'usuario' => $usuario,
            'user_roles' => $roles,
            'ranking' => $ranking,
            'miPosicion' => $miPosicion,
            'total_participantes' => $total_participantes,
            'periodo' => $periodo_actual
        ]);
    }

    private function obtenerRankingCompleto($periodo)
    {
        $ranking = DB::table('rankings as r')
            ->join('usuarios as u', 'r.Id_usuario', '=', 'u.Id_usuario')
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
            ->orderBy('r.Posicion', 'ASC')
            ->get();

        // Convertir a array
        return $ranking->toArray();
    }

    private function obtenerPosicionUsuario($usuarioId, $periodo)
    {
        $posicion = DB::table('rankings as r')
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

        return $posicion;
    }

    private function marcarUsuarioActual($ranking, $usuarioId)
    {
        foreach ($ranking as $participante) {
            $participante->es_usuario_actual = ($participante->Id_usuario == $usuarioId);
        }
        return $ranking;
    }

    private function obtenerDatosEjemploRanking()
    {
        return [
            (object)[
                'Id_ranking' => 32,
                'Posicion' => 1,
                'Periodo' => 'Octubre 2025',
                'Id_usuario' => 4,
                'Total_puntos_acumulados' => 9800,
                'periodo_date' => '2025-10-01',
                'Nombre' => 'Ana',
                'Apellido' => 'García',
                'Email' => 'ana@ejemplo.com',
                'es_usuario_actual' => false
            ],
            (object)[
                'Id_ranking' => 34,
                'Posicion' => 2,
                'Periodo' => 'Octubre 2025',
                'Id_usuario' => 1,
                'Total_puntos_acumulados' => 900,
                'periodo_date' => '2025-10-01',
                'Nombre' => 'Carlos',
                'Apellido' => 'López',
                'Email' => 'carlos@ejemplo.com',
                'es_usuario_actual' => false
            ],
            (object)[
                'Id_ranking' => 33,
                'Posicion' => 3,
                'Periodo' => 'Octubre 2025',
                'Id_usuario' => 2,
                'Total_puntos_acumulados' => 800,
                'periodo_date' => '2025-10-01',
                'Nombre' => 'María',
                'Apellido' => 'Rodríguez',
                'Email' => 'maria@ejemplo.com',
                'es_usuario_actual' => false
            ],
            (object)[
                'Id_ranking' => 35,
                'Posicion' => 4,
                'Periodo' => 'Octubre 2025',
                'Id_usuario' => 5,
                'Total_puntos_acumulados' => 0,
                'periodo_date' => '2025-10-01',
                'Nombre' => 'Juan',
                'Apellido' => 'Martínez',
                'Email' => 'juan@ejemplo.com',
                'es_usuario_actual' => false
            ],
            (object)[
                'Id_ranking' => 36,
                'Posicion' => 5,
                'Periodo' => 'Octubre 2025',
                'Id_usuario' => 6,
                'Total_puntos_acumulados' => 0,
                'periodo_date' => '2025-10-01',
                'Nombre' => 'Laura',
                'Apellido' => 'Hernández',
                'Email' => 'laura@ejemplo.com',
                'es_usuario_actual' => false
            ],
            (object)[
                'Id_ranking' => 37,
                'Posicion' => 6,
                'Periodo' => 'Octubre 2025',
                'Id_usuario' => 7,
                'Total_puntos_acumulados' => 0,
                'periodo_date' => '2025-10-01',
                'Nombre' => 'Pedro',
                'Apellido' => 'Gómez',
                'Email' => 'pedro@ejemplo.com',
                'es_usuario_actual' => false
            ],
            (object)[
                'Id_ranking' => 38,
                'Posicion' => 7,
                'Periodo' => 'Octubre 2025',
                'Id_usuario' => 9,
                'Total_puntos_acumulados' => 0,
                'periodo_date' => '2025-10-01',
                'Nombre' => 'Sofía',
                'Apellido' => 'Díaz',
                'Email' => 'sofia@ejemplo.com',
                'es_usuario_actual' => false
            ],
            (object)[
                'Id_ranking' => 39,
                'Posicion' => 8,
                'Periodo' => 'Octubre 2025',
                'Id_usuario' => 12,
                'Total_puntos_acumulados' => 0,
                'periodo_date' => '2025-10-01',
                'Nombre' => 'Miguel',
                'Apellido' => 'Torres',
                'Email' => 'miguel@ejemplo.com',
                'es_usuario_actual' => false
            ],
            (object)[
                'Id_ranking' => 40,
                'Posicion' => 9,
                'Periodo' => 'Octubre 2025',
                'Id_usuario' => 15,
                'Total_puntos_acumulados' => 0,
                'periodo_date' => '2025-10-01',
                'Nombre' => 'Elena',
                'Apellido' => 'Ramírez',
                'Email' => 'elena@ejemplo.com',
                'es_usuario_actual' => false
            ]
        ];
    }

    private function obtenerMiPosicionEjemplo($usuarioId)
    {
        // Para el ejemplo, asignar al usuario actual la posición 2 (Carlos López)
        return (object)[
            'Id_ranking' => 34,
            'Posicion' => 2,
            'Periodo' => 'Octubre 2025',
            'Id_usuario' => $usuarioId,
            'Total_puntos_acumulados' => 900,
            'periodo_date' => '2025-10-01',
            'Nombre' => session('usuario')->Nombre,
            'Apellido' => session('usuario')->Apellido,
            'Email' => session('usuario')->Email
        ];
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

        $periodo_actual = "Octubre 2025";

        try {
            $ranking_estudiantes = $this->obtenerRankingEstudiantesDocente($usuario->Id_usuario, $periodo_actual);
            
            $estadisticas = [
                'total_estudiantes' => count($ranking_estudiantes),
                'periodo_actual' => $periodo_actual,
                'curso_mas_popular' => $this->obtenerCursoMasPopular($usuario->Id_usuario)
            ];

        } catch (\Exception $e) {
            $ranking_estudiantes = $this->obtenerDatosEjemploRanking();
            $estadisticas = [
                'total_estudiantes' => count($ranking_estudiantes),
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

        $periodo_actual = "Octubre 2025";

        try {
            $ranking_completo = $this->obtenerRankingCompleto($periodo_actual);
            $estadisticas_sistema = $this->obtenerEstadisticasSistema();

            // CORREGIDO: Usar count() en lugar de ->count()
            if (count($ranking_completo) === 0) {
                $ranking_completo = $this->obtenerDatosEjemploRanking();
            }

        } catch (\Exception $e) {
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

    private function obtenerRankingEstudiantesDocente($docenteId, $periodo)
    {
        return $this->obtenerDatosEjemploRanking();
    }

    private function obtenerCursoMasPopular($docenteId)
    {
        return DB::table('cursos')
            ->where('docente_id', $docenteId)
            ->orderBy('inscripciones_count', 'DESC')
            ->first();
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

    private function obtenerEstadisticasSistemaEjemplo()
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
        $periodo = $periodo ?? "Octubre 2025";
        
        try {
            $ranking = $this->obtenerRankingCompleto($periodo);
            
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