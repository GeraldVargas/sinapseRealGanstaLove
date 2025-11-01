<?php
// app/Models/ProgresoEvaluacion.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgresoEvaluacion extends Model
{
    protected $table = 'progreso_evaluacion';
    protected $primaryKey = 'Id_progreso_evaluacion';
    
    protected $fillable = [
        'Id_evaluacion',
        'Puntaje_obtenido',
        'Porcentaje',
        'Aprobado',
        'Fecha_completado'
    ];

    public $timestamps = false;

    public function evaluacion()
    {
        return $this->belongsTo(Evaluacion::class, 'Id_evaluacion');
    }

    public function progresoCurso()
    {
        return $this->belongsTo(ProgresoCurso::class, 'Id_progreso');
    }
}