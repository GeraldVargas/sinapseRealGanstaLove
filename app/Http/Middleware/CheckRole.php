<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        $userRoles = session('user_roles', []);
        
        if (!in_array($role, $userRoles)) {
            return redirect('/login')->with('error', 'No tienes permisos para acceder a esta área.');
        }
        
        return $next($request);
    }
}