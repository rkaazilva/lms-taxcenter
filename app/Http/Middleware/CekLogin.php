<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CekLogin
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('isLoggedIn') || session('isLoggedIn') !== true) {
            return redirect()->route('login')->with('error', 'Silakan login dulu!');
        }
        return $next($request);
    }
}