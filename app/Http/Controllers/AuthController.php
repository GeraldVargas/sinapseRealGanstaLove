<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use App\Models\Rol;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLogin()
    {
        // Verificar si ya está logueado SIN redirección automática
        if (session('usuario')) {
            return view('auth.login')->with('info', 'Ya tienes una sesión activa.');
        }
        
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,Email',
            'password' => 'required|min:6|confirmed',
            'tipo_usuario' => 'required|in:Estudiante,Docente'
        ]);

        try {
            // Crear nuevo usuario
            $usuario = Usuario::create([
                'Nombre' => $request->nombre,
                'Apellido' => $request->apellido,
                'Email' => $request->email,
                'Contraseña' => Hash::make($request->password),
                'Fecha_registro' => now()->format('Y-m-d'),
                'Estado' => 1
            ]);

            // Asignar rol según selección
            $rol = Rol::where('Nombre', $request->tipo_usuario)->first();
            if ($rol) {
                $usuario->roles()->attach($rol->Id_rol);
            }

            // Auto-login después del registro
            $usuario->load('roles');
            $roles = $usuario->roles->pluck('Nombre')->toArray();
            
            session([
                'usuario' => $usuario,
                'user_roles' => $roles
            ]);

            // Redirigir según rol
            if (in_array('Docente', $roles)) {
                return redirect('/docente/dashboard')->with('success', '¡Registro exitoso!');
            } else {
                return redirect('/estudiante/dashboard')->with('success', '¡Registro exitoso!');
            }

        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Error al crear el usuario: ' . $e->getMessage()]);
        }
    }

    public function login(Request $request)
    {
        // Debug
        Log::info('=== INTENTO DE LOGIN ===');
        Log::info('Email: ' . $request->email);

        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $usuario = Usuario::where('Email', $request->email)->first();

        if (!$usuario) {
            Log::info('Usuario no encontrado: ' . $request->email);
            return back()->withErrors(['email' => 'Usuario no encontrado.']);
        }

        Log::info('Usuario encontrado: ' . $usuario->Nombre);
        Log::info('Contraseña en BD: ' . substr($usuario->Contraseña, 0, 20) . '...');

        // Verificar contraseña
        $passwordValido = false;

        if (Hash::check($request->password, $usuario->Contraseña)) {
            $passwordValido = true;
            Log::info('Contraseña válida (Bcrypt)');
        }
        else if ($request->password === $usuario->Contraseña) {
            $passwordValido = true;
            Log::info('Contraseña válida (Texto plano)');
            // Actualizar a Bcrypt
            $usuario->update([
                'Contraseña' => Hash::make($request->password)
            ]);
        }

        if ($passwordValido) {
            $usuario->load('roles');
            $roles = $usuario->roles->pluck('Nombre')->toArray();
            
            Log::info('Roles del usuario: ' . implode(', ', $roles));
            
            session([
                'usuario' => $usuario,
                'user_roles' => $roles
            ]);

            Log::info('Sesión creada exitosamente');

            // Redirigir según el rol
            if (in_array('Administrador', $roles)) {
                Log::info('Redirigiendo a admin dashboard');
                return redirect('/admin/dashboard');
            } elseif (in_array('Docente', $roles)) {
                Log::info('Redirigiendo a docente dashboard');
                return redirect('/docente/dashboard');
            } else {
                Log::info('Redirigiendo a estudiante dashboard');
                return redirect('/estudiante/dashboard');
            }
        }

        Log::info('Contraseña inválida');
        return back()->withErrors(['email' => 'Contraseña incorrecta.']);
    }

    public function logout()
    {
        session()->flush();
        return redirect('/')->with('success', 'Sesión cerrada correctamente.');
    }
}