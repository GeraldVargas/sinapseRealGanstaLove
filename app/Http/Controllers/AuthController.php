<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\Usuario;

class AuthController extends Controller
{
    public function showLogin()
    {
        Log::info('=== MOSTRANDO FORMULARIO LOGIN ===');
        return view('auth.login');
    }

    public function login(Request $request)
    {
        Log::info('=== INICIO PROCESO LOGIN ===');
        Log::info('Email: ' . $request->email);

        // Validación
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Buscar usuario
        $usuario = Usuario::where('Email', $request->email)->first();

        if (!$usuario) {
            Log::info('USUARIO NO ENCONTRADO: ' . $request->email);
            return back()->withErrors(['email' => 'Usuario no encontrado']);
        }

        Log::info('Usuario encontrado: ' . $usuario->Nombre . ' ' . $usuario->Apellido);
        Log::info('Contraseña BD: ' . substr($usuario->Contraseña, 0, 20) . '...');

        // Verificar contraseña
        $passwordValido = Hash::check($request->password, $usuario->Contraseña);
        Log::info('Password válido (Hash): ' . ($passwordValido ? 'SÍ' : 'NO'));

        // Si no pasa con Hash, probar texto plano
        if (!$passwordValido) {
            $passwordValido = ($request->password === $usuario->Contraseña);
            Log::info('Password válido (Texto plano): ' . ($passwordValido ? 'SÍ' : 'NO'));
        }

        if ($passwordValido) {
            Log::info('=== CONTRASEÑA VÁLIDA ===');

            // Cargar relaciones básicas
            try {
                $usuario->load('roles');
                Log::info('Roles cargados: ' . $usuario->roles->count());
            } catch (\Exception $e) {
                Log::error('Error cargando roles: ' . $e->getMessage());
                $usuario->roles = collect();
            }

            // Crear sesión
            session([
                'usuario' => $usuario,
                'user_roles' => $usuario->roles->pluck('Nombre')->toArray(),
                'user_insignias' => collect()
            ]);

            Log::info('Sesión creada exitosamente');
            Log::info('Redirigiendo a dashboard...');

            // Redirigir al dashboard
            return redirect('/dashboard')->with('success', '¡Bienvenido!');

        } else {
            Log::info('=== CONTRASEÑA INVÁLIDA ===');
            return back()->withErrors(['email' => 'Contraseña incorrecta']);
        }
    }

    public function logout()
    {
        session()->flush();
        return redirect('/')->with('success', 'Sesión cerrada');
    }
}