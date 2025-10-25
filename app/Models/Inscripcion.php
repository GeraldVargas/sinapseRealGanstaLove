<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    use HasFactory;

    protected $table = 'inscripciones';
    protected $primaryKey = 'id_inscripc';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'id_curso',
        'Fecha_inscripcion',
        'Estado'
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