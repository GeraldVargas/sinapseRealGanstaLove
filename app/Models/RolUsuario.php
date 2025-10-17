<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolUsuario extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rol_usuario'; // <-- Indica el nombre exacto de la tabla.

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_rol_usuario'; // <-- Indica el nombre de tu ID.

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false; // <-- Indica a Laravel que no busque created_at y updated_at.
}