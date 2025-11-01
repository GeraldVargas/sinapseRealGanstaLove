<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgresoCurso extends Model
{
    use HasFactory;

    protected $table = 'progreso_curso';
    protected $primaryKey = 'Id_progreso';
    
    protected $fillable = [
        'Id_usuario',
        'Id_curso',
        'Fecha_actualizacion',
        'Porcentaje',
        'Nivel',
        'Modulos_completados',
        'Temas_completados',
        'Evaluaciones_superadas',
        'Actividades_superadas'
    ];

    public $timestamps = false;

    public function usuario()
    {
        return $this->belongsTo(User::class, 'Id_usuario');
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'Id_curso');
    }
}