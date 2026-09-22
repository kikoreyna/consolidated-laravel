<?php

namespace App\Http\Middleware;

use Closure;

class NotClientMiddleware
{
    public function handle($request, Closure $next)
    {
        if ($request->user() && $request->user()->rol === 'cliente') {
            abort(403);
        }

        return $next($request);
    }
}
