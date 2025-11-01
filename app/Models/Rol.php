<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;

    protected $table = 'roles';
    protected $primaryKey = 'id_rol';
    
    protected $fillable = [
        'Nombre',
        'Descripcion'
    ];

    public $timestamps = false;

    public function usuarios()
    {
        return $this->belongsToMany(Usuario::class, 'rol_usuario', 'id_rol', 'id_usuario');
    }
}