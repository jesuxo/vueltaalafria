<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(isset(auth()->user()->type) and (auth()->user()->type === 'admin' or auth()->user()->type === 'usuario')){
            $comercialid = session('comercialid');
            if(!$comercialid) {
                session(['comercialid' => 1]);
                $comercialid = 1;
            }
            return $next($request);
        }

        return redirect('login');
    }
}
