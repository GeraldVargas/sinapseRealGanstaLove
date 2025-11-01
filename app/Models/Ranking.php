<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ranking extends Model
{
    use HasFactory;

    protected $table = 'ranking';
    protected $primaryKey = 'Id_ranking';
    
    protected $fillable = [
        'Posicion', 
        'Periodo'
    ];

    public $timestamps = false;

    public function gestionPuntos()
    {
        return $this->hasMany(GestionPuntos::class, 'Id_ranking', 'Id_ranking');
    }
}