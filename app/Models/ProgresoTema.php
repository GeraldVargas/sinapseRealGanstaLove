<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgresoTema extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'progreso_tema'; // <-- Indica el nombre exacto de la tabla.

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_progreso_tema'; // <-- Indica el nombre de tu ID.
}