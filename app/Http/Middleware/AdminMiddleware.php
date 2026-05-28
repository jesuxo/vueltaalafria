<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || auth()->user()->type !== 'admin') {
            return redirect('/')->with('error', 'No tienes acceso al panel administrativo');
        }

        return $next($request);
    }
}
