<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $table = 'pago';
    protected $primaryKey = 'Id_pago';
    public $timestamps = false;

    protected $fillable = [
        'Id_inscripcion',
        'Monto',
        'Puntos_usad',
        'Modalid',
        'Fecha_pago',
        'Estado',
        'Metodo'
    ];

    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class, 'Id_inscripcion');
    }
}