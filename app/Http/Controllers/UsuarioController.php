<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Rol;

class UsuarioController extends Controller
{
    public function estudiantes()
    {
        $estudiantes = Usuario::whereHas('roles', function($query) {
            $query->where('Nombre', 'Estudiante');
        })->get();

        return view('listados.estudiantes', compact('estudiantes'));
    }

    public function docentes()
    {
        $docentes = Usuario::whereHas('roles', function($query) {
            $query->where('Nombre', 'Docente');
        })->get();

        return view('listados.docentes', compact('docentes'));
    }
}