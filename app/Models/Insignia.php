<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insignia extends Model
{
    use HasFactory;

    protected $table = 'insignia';
    protected $primaryKey = 'Id_insignia';
    public $timestamps = false;

    protected $fillable = [
        'Nombre',
        'Descripcion',
        'Valor_Puntos',
        'Dificultad',
        'Categoria',
        'Imagen'
    ];

    // Relación con usuarios - CORREGIDO
    public function usuarios()
    {
        return $this->belongsToMany(
            Usuario::class, 
            'usuario_insignia', 
            'Id_insignia',     // Foreign key en usuario_insignia
            'Id_usuario',      // Foreign key en usuario_insignia
            'Id_insignia',     // Local key
            'Id_usuario'       // Related key
        )->withPivot('Fecha_obtencion', 'Puntos_Obtenidos');
    }
}