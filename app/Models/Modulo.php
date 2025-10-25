<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    use HasFactory;

    protected $table = 'modulos';
    protected $primaryKey = 'Id_modulo';
    public $timestamps = false;

    protected $fillable = [
        'id_curso',
        'Nombre',
        'Descripcion'
    ];

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'id_curso');
    }

    public function temas()
    {
        return $this->hasMany(Tema::class, 'id_modulo');
    }
}