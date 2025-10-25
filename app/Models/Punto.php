<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Punto extends Model
{
    use HasFactory;

    protected $table = 'puntos';
    protected $primaryKey = 'id_punto';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'cantidad',
        'tipo', // 'ganancia' o 'canje'
        'concepto',
        'fecha',
        'id_curso',
        'id_modulo',
        'id_tema'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'id_curso');
    }
}