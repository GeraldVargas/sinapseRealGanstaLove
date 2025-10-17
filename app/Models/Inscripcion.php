<?php

namespace App\Models; // <-- ¡Esta línea es CRUCIAL! Debe ser App\Models

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_inscripcion'; // <-- El ajuste que hicimos antes.
}