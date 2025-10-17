<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgresoEvaluacion extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'progreso_evaluacion'; // <-- Indica el nombre exacto de la tabla.

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_progreso_evaluacion'; // <-- Indica el nombre de tu ID.
}