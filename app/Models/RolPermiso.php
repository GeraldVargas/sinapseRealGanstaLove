<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolPermiso extends Model
{
    use HasFactory;

    // El nombre de la tabla 'rol_permisos' coincide con el modelo, así que no es necesario especificarlo.

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false; // <-- Indica a Laravel que no busque las columnas created_at y updated_at.

}