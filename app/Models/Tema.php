<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tema extends Model
{
    use HasFactory;

    protected $table = 'temas';
    protected $primaryKey = 'Id_tema';
    public $timestamps = false;

    protected $fillable = [
        'id_modulo',
        'Nombre',
        'Descripcion',
        'Orden',
        'Contenido'
    ];

    public function modulo()
    {
        return $this->belongsTo(Modulo::class, 'id_modulo');
    }

    public function evaluaciones()
    {
        return $this->hasMany(Evaluacion::class, 'id_tema');
    }
}