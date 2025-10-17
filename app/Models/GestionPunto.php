<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GestionPunto extends Model
{
    use HasFactory;

    // Laravel asume que la tabla se llama 'gestion_puntos', lo cual es correcto.

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_gestion'; // <-- ¡Añade esta línea!
}