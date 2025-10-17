<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pago'; // <-- Indica el nombre singular de la tabla.

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_pago'; // <-- Indica el nombre de tu ID.
}