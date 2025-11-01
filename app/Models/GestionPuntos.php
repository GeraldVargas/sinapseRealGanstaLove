<?php
// app/Models/GestionPuntos.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GestionPuntos extends Model
{
    use HasFactory;

    protected $table = 'gestion_puntos';
    protected $primaryKey = 'Id_gestion';

    protected $fillable = [
        'Id_usuario',
        'Id_ranking',
        'Total_puntos_actual',
        'Total_saldo_usado',
        'Total_puntos_acumulados',
        'puntos_acumulados_mes',
        'puntos_acumulados_total',
        'puntos_canjeados'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'Id_usuario');
    }

    public function ranking()
    {
        return $this->belongsTo(Ranking::class, 'Id_ranking');
    }
}