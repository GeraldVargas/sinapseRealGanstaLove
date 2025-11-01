<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Evaluacion;

class Entrega extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'entregas';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'Id_entrega';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'Id_evaluacion',
        'Id_usuario',
        'Archivo',
        'Descripcion',
        'Fecha_entrega',
        'Puntos_asignados',
        'Estado',
        'Comentario_docente',
        'Fecha_calificacion'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'Fecha_entrega' => 'datetime',
        'Fecha_calificacion' => 'datetime',
        'Puntos_asignados' => 'integer'
    ];

    /**
     * Obtener la evaluación asociada a esta entrega.
     */
    public function evaluacion()
    {
        return $this->belongsTo(Evaluacion::class, 'Id_evaluacion', 'Id_evaluacion');
    }

    /**
     * Obtener el usuario (estudiante) que realizó la entrega.
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'Id_usuario', 'Id_usuario');
    }

    /**
     * Scope para obtener solo entregas pendientes.
     */
    public function scopePendientes($query)
    {
        return $query->where('Estado', 'pendiente');
    }

    /**
     * Scope para obtener entregas calificadas.
     */
    public function scopeCalificadas($query)
    {
        return $query->where('Estado', 'calificado');
    }

    /**
     * Scope para obtener entregas por evaluación.
     */
    public function scopePorEvaluacion($query, $idEvaluacion)
    {
        return $query->where('Id_evaluacion', $idEvaluacion);
    }

    /**
     * Scope para obtener entregas por usuario.
     */
    public function scopePorUsuario($query, $idUsuario)
    {
        return $query->where('Id_usuario', $idUsuario);
    }

    /**
     * Verificar si la entrega está pendiente.
     */
    public function estaPendiente()
    {
        return $this->Estado === 'pendiente';
    }

    /**
     * Verificar si la entrega está calificada.
     */
    public function estaCalificada()
    {
        return $this->Estado === 'calificado';
    }

    /**
     * Calificar la entrega.
     */
    public function calificar($puntos, $comentario = null)
    {
        $this->update([
            'Puntos_asignados' => $puntos,
            'Comentario_docente' => $comentario,
            'Estado' => 'calificado',
            'Fecha_calificacion' => now()
        ]);

        return $this;
    }

    /**
     * Obtener el nombre del archivo sin la ruta.
     */
    public function getNombreArchivoAttribute()
    {
        return $this->Archivo ? basename($this->Archivo) : null;
    }

    /**
     * Obtener la ruta completa del archivo.
     */
    public function getRutaArchivoAttribute()
    {
        return $this->Archivo ? storage_path('app/public/entregas/' . $this->Archivo) : null;
    }

    /**
     * Obtener la URL del archivo para descargar.
     */
    public function getUrlDescargaAttribute()
    {
        return $this->Archivo ? asset('storage/entregas/' . $this->Archivo) : null;
    }
}