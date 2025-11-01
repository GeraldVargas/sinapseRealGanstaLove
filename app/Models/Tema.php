<?php
// app/Models/Tema.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tema extends Model
{
    protected $table = 'temas';
    protected $primaryKey = 'Id_tema';
    
    protected $fillable = [
        'Id_modulo',
        'Nombre',
        'Descripcion',
        'Orden',
        'Contenido'
    ];

    public $timestamps = false;

    public function modulo()
    {
        return $this->belongsTo(Modulo::class, 'Id_modulo');
    }

    public function evaluaciones()
    {
        return $this->hasMany(Evaluacion::class, 'Id_tema');
    }

    public function curso()
    {
        return $this->hasOneThrough(
            Curso::class,
            Modulo::class,
            'Id_modulo',
            'Id_curso',
            'Id_modulo',
            'Id_curso'
        );
    }
}