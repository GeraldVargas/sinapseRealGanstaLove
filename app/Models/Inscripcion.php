<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    use HasFactory;

    protected $table = 'inscripciones';
    protected $primaryKey = 'Id_inscripcion';
    
    protected $fillable = [
        'Id_usuario',
        'Id_curso',
        'Fecha_inscripcion',
        'Estado'
    ];

    public $timestamps = false;

    public function usuario()
    {
        return $this->belongsTo(User::class, 'Id_usuario');
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'Id_curso');
    }
}