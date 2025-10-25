<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgresoTema extends Model
{
    use HasFactory;

    protected $table = 'progreso_tema';
    protected $primaryKey = 'id_progreso';
    public $timestamps = false;

    protected $fillable = [
        'Id_tema',
        'completado',
        'Fecha_completado'
    ];

    public function tema()
    {
        return $this->belongsTo(Tema::class, 'Id_tema');
    }
}