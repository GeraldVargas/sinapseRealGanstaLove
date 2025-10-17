<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insignia extends Model
{
    use HasFactory;

    // Laravel asume que la tabla se llama 'insignias', pero tu tabla es 'insignia'.
    protected $table = 'insignia';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_insignia'; // <-- ¡Añade esta línea!
}