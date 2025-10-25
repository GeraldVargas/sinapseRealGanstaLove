<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Verificación manual de sesión
        if (!session('usuario')) {
            return redirect('/login')->with('error', 'Debes iniciar sesión primero.');
        }

        $usuario = session('usuario');
        $roles = session('user_roles', []);

        // Verificar que sea admin
        if (!in_array('Administrador', $roles)) {
            return redirect('/login')->with('error', 'No tienes acceso a esta área.');
        }

        return view('admin.dashboard', [
            'usuario' => $usuario,
            'user_roles' => $roles
        ]);
    }
}