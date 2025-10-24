<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    protected $table = 'usuarios';
    protected $primaryKey = 'Id_usuario';
    public $timestamps = false;

    protected $fillable = [
        'Nombre',
        'Apellido', 
        'Email',
        'Contraseña',
        'Fecha_registro',
        'Estado'
    ];

    protected $hidden = [
        'Contraseña'
    ];

    // Relación con roles
    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'rol_usuario', 'id_usuario', 'id_rol');
    }

    // Relación con cursos
    public function cursos()
    {
        return $this->belongsToMany(Curso::class, 'inscripciones', 'id_usuario', 'id_curso');
    }

    // Relación con insignias - CORREGIDO
    public function insignias()
    {
        return $this->belongsToMany(
            Insignia::class, 
            'usuario_insignia', 
            'Id_usuario',      // Foreign key en usuario_insignia
            'Id_insignia',     // Foreign key en usuario_insignia
            'Id_usuario',      // Local key
            'Id_insignia'      // Related key
        )->withPivot('Fecha_obtencion', 'Puntos_Obtenidos');
    }
}