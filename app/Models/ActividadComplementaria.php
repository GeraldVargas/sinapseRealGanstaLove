<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActividadComplementaria extends Model
{
    use HasFactory;

    protected $table = 'actividades_complementarias';
    protected $primaryKey = 'Id_actividad';
    public $timestamps = false;

    protected $fillable = [
        'id_curso',
        'Tipo',
        'Descripcion',
        'Fecha'
    ];

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'id_curso');
    }
}