<?php
// filepath: d:\ProyectoTbd\sinapsereal\app\Http\Controllers\Auth\LoginController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login'); // Asegúrate de tener esta vista
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
        'Email' => ['required', 'email'],
        'Conitrac' => ['required'],
    ]);

    if (Auth::attempt([
        'Email' => $credentials['Email'],
        'Conitrac' => $credentials['Conitrac']
    ])) {
        $request->session()->regenerate();
        return redirect()->intended('dashboard');
    }

    return back()->withErrors([
        'Email' => 'Las credenciales no coinciden con nuestros registros.',
    ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/'); // Redirige a la página de inicio
    }
}