<?php
// app/Models/Evaluacion.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluacion extends Model
{
    protected $table = 'evaluaciones';
    protected $primaryKey = 'Id_evaluacion';
    
    protected $fillable = [
        'Id_modulo', // CORREGIDO: según tu estructura real
        'Tipo',
        'Puntaje_maximo', 
        'Fecha_inicio',
        'Fecha_fin'
    ];

    public $timestamps = false;

    // RELACIÓN DIRECTA CON MÓDULO
    public function modulo()
    {
        return $this->belongsTo(Modulo::class, 'Id_modulo');
    }

    // RELACIÓN CON CURSO a través del módulo
    public function curso()
    {
        return $this->hasOneThrough(
            Curso::class,
            Modulo::class,
            'Id_modulo', // Foreign key on modulos table
            'Id_curso', // Foreign key on cursos table
            'Id_modulo', // Local key on evaluaciones table
            'Id_curso' // Local key on modulos table
        );
    }

    // RELACIÓN CON PROGRESOS
    public function progresos()
    {
        return $this->hasMany(ProgresoEvaluacion::class, 'Id_evaluacion');
    }
}