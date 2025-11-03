<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SimpleLoginMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Permitir acceder a la página de login sin estar autenticado
        if ($request->is('admin/login')) {
            return $next($request);
        }

        // Verificar si el usuario está autenticado mediante sesión simple
        if (!$request->session()->get('simple_login', false)) {
            return redirect('/admin/login');
        }

        return $next($request);
    }
}