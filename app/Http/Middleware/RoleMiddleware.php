<?php

namespace App\Http\Middleware;

use Closure;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user || !$user->activo || !in_array($user->rol, $roles, true)) {
            abort(403);
        }

        return $next($request);
    }
}
