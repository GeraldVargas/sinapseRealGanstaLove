<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    use HasFactory;

    protected $table = 'cursos';
    protected $primaryKey = 'Id_curso';
    public $timestamps = false;

    protected $fillable = [
        'Titulo',
        'Descripc',
        'Duracion',
        'Costo',
        'Estado'
    ];

    // Relación con módulos
    public function modulos()
    {
        return $this->hasMany(Modulo::class, 'id_curso');
    }

    // Relación con inscripciones
    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class, 'id_curso');
    }

    // Relación con actividades
    public function actividades()
    {
        return $this->hasMany(ActividadComplementaria::class, 'id_curso');
    }
}