<?php
// app/Http/Middleware/TeamAuth.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TeamAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('team_id')) {
            return redirect()->route('team.login')->with('error', 'Debes iniciar sesión primero');
        }

        return $next($request);
    }
}
