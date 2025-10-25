<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Canje extends Model
{
    use HasFactory;

    protected $table = 'canjes';
    protected $primaryKey = 'id_canje';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'id_recompensa',
        'Fecha_canje',
        'Estado'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function recompensa()
    {
        return $this->belongsTo(Recompensa::class, 'id_recompensa');
    }
}