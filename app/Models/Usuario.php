<?php
// app/Models/Usuario.php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'Id_usuario';

    protected $fillable = [
        'Nombre',
        'Apellido',
        'Email',
        'Contraseña',
        'Estado'
    ];

    protected $hidden = [
        'Contraseña',
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->Contraseña;
    }

    // RELACIÓN CON ROLES
    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'rol_usuario', 'Id_usuario', 'Id_rol');
    }

    // VERIFICAR SI TIENE UN ROL ESPECÍFICO
    public function tieneRol($rol)
    {
        return $this->roles()->where('Nombre', $rol)->exists();
    }

    // VERIFICAR SI ES ESTUDIANTE
        public function esEstudiante()
    {
        // Si ya tienes una relación con roles, usa esta:
        if (method_exists($this, 'roles')) {
            return $this->roles()->where('Nombre', 'estudiante')->exists();
        }
        
        // Si no, verifica directamente en la tabla rol_usuario
        return DB::table('rol_usuario')
            ->where('Id_usuario', $this->Id_usuario)
            ->whereIn('Id_rol', function($query) {
                $query->select('Id_rol')
                    ->from('roles')
                    ->where('Nombre', 'estudiante');
            })
            ->exists();
    }

    // VERIFICAR SI ES DOCENTE
    public function esDocente()
    {
        if (method_exists($this, 'roles')) {
            return $this->roles()->where('Nombre', 'docente')->exists();
        }
        
        return DB::table('rol_usuario')
            ->where('Id_usuario', $this->Id_usuario)
            ->whereIn('Id_rol', function($query) {
                $query->select('Id_rol')
                    ->from('roles')
                    ->where('Nombre', 'docente');
            })
            ->exists();
    }

    // VERIFICAR SI ES ADMIN
    public function esAdmin()
    {
        if (method_exists($this, 'roles')) {
            return $this->roles()->where('Nombre', 'admin')->exists();
        }
        
        return DB::table('rol_usuario')
            ->where('Id_usuario', $this->Id_usuario)
            ->whereIn('Id_rol', function($query) {
                $query->select('Id_rol')
                    ->from('roles')
                    ->where('Nombre', 'admin');
            })
            ->exists();
    }

    // OBTENER EL ROL PRINCIPAL
    public function rolPrincipal()
    {
        return $this->roles()->first();
    }

    // Relaciones existentes
    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class, 'Id_usuario');
    }

    public function progresos()
    {
        return $this->hasMany(ProgresoCurso::class, 'Id_usuario');
    }

    public function gestionPuntos()
    {
        return $this->hasOne(GestionPuntos::class, 'Id_usuario');
    }

    public function insignias()
    {
        return $this->hasMany(UsuarioInsignia::class, 'Id_usuario');
    }

    public function canjes()
    {
        return $this->hasMany(Canje::class, 'Id_usuario');
    }
}