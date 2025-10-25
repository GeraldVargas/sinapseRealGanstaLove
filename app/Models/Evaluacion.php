<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluacion extends Model
{
    use HasFactory;

    protected $table = 'evaluaciones';
    protected $primaryKey = 'id_evaluacion';
    public $timestamps = false;

    protected $fillable = [
        'id_tema',
        'Tipo',
        'Puntaje_maximo',
        'Fecha_inicio',
        'fecha_fin'
    ];

    public function tema()
    {
        return $this->belongsTo(Tema::class, 'id_tema');
    }
}