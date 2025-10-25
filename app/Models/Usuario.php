<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    protected $table = 'usuarios';
    protected $primaryKey = 'Id_usuario';
    public $timestamps = false;

    protected $fillable = [
        'Nombre',
        'Apellido', 
        'Email',
        'Contraseña',
        'Fecha_registro',
        'Estado'
    ];

    protected $hidden = [
        'Contraseña'
    ];

    // Relación con roles
    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'rol_usuario', 'id_usuario', 'id_rol');
    }

    // Relación con cursos a través de inscripciones
    public function cursos()
    {
        return $this->belongsToMany(Curso::class, 'inscripciones', 'id_usuario', 'id_curso');
    }

    // Relación con insignias
    public function insignias()
    {
        return $this->belongsToMany(Insignia::class, 'usuario_insignia', 'Id_usuario', 'Id_insignia')
                    ->withPivot('Fecha_obtencion', 'Puntos_Obtenidos');
    }

    // Relación con progresos de curso
    public function progresos()
    {
        return $this->hasMany(ProgresoCurso::class, 'id_Usuario');
    }

    // Relación con progresos de tema
    public function progresosTema()
    {
        return $this->hasMany(ProgresoTema::class, 'id_usuario');
    }

    // Relación con progresos de evaluación
    public function progresosEvaluacion()
    {
        return $this->hasMany(ProgresoEvaluacion::class, 'id_usuario');
    }

    // Relación con inscripciones
    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class, 'id_usuario');
    }

    // Relación con gestión de puntos
    public function gestionPuntos()
    {
        return $this->hasMany(GestionPunto::class, 'id_usuario');
    }

    // Relación con canjes
    public function canjes()
    {
        return $this->hasMany(Canje::class, 'id_usuario');
    }

    // Relación con ranking
    public function ranking()
    {
        return $this->hasOne(Ranking::class, 'id_usuario');
    }

    // Calcular puntos totales desde gestión_puntos
    public function getPuntosTotalesAttribute()
    {
        $gestion = $this->gestionPuntos()->first();
        return $gestion ? $gestion->Total_saldo : 0;
    }

    // Obtener recompensas canjeadas
    public function recompensasCanjeadas()
    {
        return $this->belongsToMany(Recompensa::class, 'canjes', 'id_usuario', 'id_recompensa')
                    ->withPivot('Fecha_canje', 'Estado');
    }
}