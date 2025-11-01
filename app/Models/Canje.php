<?php
// app/Models/Canje.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Canje extends Model
{
    protected $table = 'canjes';
    protected $primaryKey = 'Id_canje';
    
    protected $fillable = [
        'Id_usuario',
        'Id_recompensa',
        'Fecha_canje',
        'Estado'
    ];

    public $timestamps = false;

    public function recompensa()
    {
        return $this->belongsTo(Recompensa::class, 'Id_recompensa');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'Id_usuario');
    }
}