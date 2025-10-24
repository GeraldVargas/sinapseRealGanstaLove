<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Authenticate
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('usuario')) {
            return redirect('/login')->with('error', 'Debes iniciar sesión');
        }
        
        return $next($request);
    }
}