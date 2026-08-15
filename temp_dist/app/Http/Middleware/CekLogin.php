<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CekLogin
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('isLoggedIn') || session('isLoggedIn') !== true) {
            // Jika permintaan AJAX / API, kembalikan JSON 401 agar frontend tidak menerima HTML login
            if ($request->expectsJson() || $request->is('siswa/api/*') || $request->ajax()) {
                return response()->json(['status' => 'unauthenticated', 'message' => 'Silakan login terlebih dahulu'], 401);
            }
            return redirect()->route('login')->with('error', 'Silakan login dulu!');
        }
        return $next($request);
    }
}