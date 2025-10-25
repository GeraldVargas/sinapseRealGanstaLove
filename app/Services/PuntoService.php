<?php

namespace App\Services;

use App\Models\Usuario;
use App\Models\GestionPunto;
use App\Models\Insignia;
use App\Models\Ranking;

class PuntoService
{
    // Asignar puntos por completar tema
    public function completarTema($usuario, $curso, $modulo, $tema)
    {
        $puntos = 10;
        $this->agregarPuntos($usuario, $puntos, "Completar tema: {$tema->Nombre}");
        $this->verificarInsignias($usuario);
        return $puntos;
    }

    // Asignar puntos por completar módulo
    public function completarModulo($usuario, $curso, $modulo)
    {
        $puntos = 50;
        $this->agregarPuntos($usuario, $puntos, "Completar módulo: {$modulo->Nombre}");
        $this->verificarInsignia($usuario, 'Maestro de Módulos');
        return $puntos;
    }

    // Asignar puntos por completar curso
    public function completarCurso($usuario, $curso)
    {
        $puntos = 200;
        $this->agregarPuntos($usuario, $puntos, "Completar curso: {$curso->Titulo}");
        $this->verificarInsignia($usuario, 'Primer Paso');
        $this->verificarInsignia($usuario, 'Constancia');
        return $puntos;
    }

    // Asignar puntos por aprobar evaluación
    public function aprobarEvaluacion($usuario, $evaluacion, $curso)
    {
        $puntos = 20;
        $this->agregarPuntos($usuario, $puntos, "Aprobar evaluación: {$evaluacion->Tipo}");
        $this->verificarInsignia($usuario, 'Estudiante Aplicado');
        return $puntos;
    }

    // Asignar puntos por asistencia
    public function registrarAsistencia($usuario, $actividad)
    {
        $puntos = 5;
        $this->agregarPuntos($usuario, $puntos, "Asistencia a: {$actividad->Tipo}");
        $this->verificarInsignia($usuario, 'Asistencia Perfecta');
        return $puntos;
    }

    // Método principal para agregar puntos
    private function agregarPuntos($usuario, $puntos, $concepto)
    {
        // Obtener o crear gestión de puntos
        $gestion = GestionPunto::firstOrCreate(
            ['id_usuario' => $usuario->Id_usuario],
            ['Total_puntos_a' => 0, 'Total_saldo' => 0]
        );

        // Actualizar puntos
        $gestion->Total_puntos_a += $puntos;
        $gestion->Total_saldo += $puntos;
        $gestion->save();

        // Actualizar ranking
        $this->actualizarRanking($usuario, $gestion->Total_saldo);
    }

    // Canjear puntos por recompensa
    public function canjearRecompensa($usuario, $recompensa)
    {
        $gestion = GestionPunto::where('id_usuario', $usuario->Id_usuario)->first();

        if (!$gestion || $gestion->Total_saldo < $recompensa->costo_puntos) {
            return false;
        }

        // Restar puntos
        $gestion->Total_saldo -= $recompensa->costo_puntos;
        $gestion->save();

        // Registrar canje
        \App\Models\Canje::create([
            'id_usuario' => $usuario->Id_usuario,
            'id_recompensa' => $recompensa->id_recompensa,
            'Fecha_canje' => now()->format('Y-m-d'),
            'Estado' => 'completado'
        ]);

        return true;
    }

    // Actualizar ranking del usuario
    private function actualizarRanking($usuario, $puntosTotales)
    {
        $ranking = Ranking::firstOrCreate(
            ['id_usuario' => $usuario->Id_usuario],
            ['Posicion' => 0, 'Periodo' => date('Y-m')]
        );

        $ranking->Total_puntos_a = $puntosTotales;
        $ranking->save();

        // Recalcular posiciones
        $this->recalcularRankings();
    }

    // Recalcular todas las posiciones del ranking
    private function recalcularRankings()
    {
        $rankings = Ranking::where('Periodo', date('Y-m'))
            ->orderBy('Total_puntos_a', 'desc')
            ->get();

        $posicion = 1;
        foreach ($rankings as $ranking) {
            $ranking->Posicion = $posicion++;
            $ranking->save();
        }
    }

    // Verificar y asignar insignias
    private function verificarInsignias($usuario)
    {
        $puntosTotales = $usuario->puntos_totales;

        if ($puntosTotales >= 1000) {
            $this->asignarInsignia($usuario, 'Gran Maestro');
        } elseif ($puntosTotales >= 500) {
            $this->asignarInsignia($usuario, 'Experto');
        } elseif ($puntosTotales >= 100) {
            $this->asignarInsignia($usuario, 'Aprendiz Avanzado');
        }
    }

    private function verificarInsignia($usuario, $nombreInsignia)
    {
        $this->asignarInsignia($usuario, $nombreInsignia);
    }

    private function asignarInsignia($usuario, $nombreInsignia)
    {
        $insignia = Insignia::where('Nombre', $nombreInsignia)->first();
        
        if ($insignia && !$usuario->insignias->contains($insignia->Id_insignia)) {
            $usuario->insignias()->attach($insignia->Id_insignia, [
                'Fecha_obtencion' => now()->format('Y-m-d'),
                'Puntos_Obtenidos' => $insignia->Valor_Puntos
            ]);
        }
    }
}