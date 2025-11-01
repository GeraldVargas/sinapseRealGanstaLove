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
        'Descripcion', 
        'Modalidad',
        'Duracion',
        'Costo',
        'Estado'
    ];

    // Relación con inscripciones
    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class, 'Id_curso');
    }

    // Relación con módulos
    public function modulos()
    {
        return $this->hasMany(Modulo::class, 'Id_curso');
    }

    // CORREGIDO: Obtener estudiantes inscritos
    public function estudiantes()
    {
        return $this->belongsToMany(
            Usuario::class, 
            'inscripciones', 
            'Id_curso',      // Foreign key en la tabla pivote (inscripciones) que referencia a cursos
            'Id_usuario'     // Foreign key en la tabla pivote (inscripciones) que referencia a usuarios
        )->whereHas('roles', function($query) {
            $query->where('Nombre', 'Estudiante');
        });
    }

    // CORREGIDO: Obtener docentes asignados
    public function docentes()
    {
        return $this->belongsToMany(
            Usuario::class, 
            'docente_curso', 
            'Id_curso',      // Foreign key en la tabla pivote
            'Id_docente'     // Foreign key en la tabla pivote
        )->whereHas('roles', function($query) {
            $query->where('Nombre', 'Docente');
        });
    }

    // Contar inscripciones
    public function getInscripcionesCountAttribute()
    {
        return $this->inscripciones()->count();
    }
    
}