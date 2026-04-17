<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    // El handle recibe la petición, el siguiente middleware y el nombre del rol a comprobar
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! $request->user()?->userRoles()->where('name', $role)->exists()) {
            abort(403, 'Acceso no autorizado para este rol.');
        }

        return $next($request);
    }
}
