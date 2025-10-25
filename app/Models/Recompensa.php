<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recompensa extends Model
{
    use HasFactory;

    protected $table = 'recompensas';
    protected $primaryKey = 'id_recompensa';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'costo_puntos',
        'tipo', // 'beca', 'descuento', 'bono', 'material'
        'estado'
    ];

    public function usuarios()
    {
        return $this->belongsToMany(Usuario::class, 'canjes', 'id_recompensa', 'id_usuario')
                    ->withPivot('fecha_canje', 'estado');
    }
}