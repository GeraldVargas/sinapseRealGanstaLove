<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GestionPunto extends Model
{
    use HasFactory;

    protected $table = 'gestion_puntos';
    protected $primaryKey = 'id_gestion_pu';
    public $timestamps = false;

    protected $fillable = [
        'id_ranking',
        'id_usuario',
        'Total_puntos_a',
        'Total_saldo'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function ranking()
    {
        return $this->belongsTo(Ranking::class, 'id_ranking');
    }
}