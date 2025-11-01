<?php
// app/Models/Recompensa.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recompensa extends Model
{
    protected $table = 'recompensas';
    protected $primaryKey = 'Id_recompe';
    
    protected $fillable = [
        'Descripc',
        'Costo_puntos',
        'Tipo'
    ];

    public $timestamps = false;

    public function canjes()
    {
        return $this->hasMany(Canje::class, 'Id_recompensa');
    }
}