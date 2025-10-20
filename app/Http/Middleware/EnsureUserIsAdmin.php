<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si el usuario está autenticado y es admin
        if (! $request->user() || ! $request->user()->isAdmin()) {
            // Si no es admin, redirigir al dashboard regular
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
