<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SatkerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()) {
            if(auth()->user()->current_role_id == 7 || auth()->user()->current_role_id == 8){ // Admin Satker
                return $next($request);
            }
            abort('403');
        }

        return redirect()->route('login');
    }
}
