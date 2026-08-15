<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!session()->has('isLoggedIn') || session('isLoggedIn') !== true) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'unauthenticated',
                    'message' => 'Silakan login terlebih dahulu.'
                ], 401);
            }
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu!');
        }

        $userRole = strtoupper(session('role', ''));
        
        $allowed = false;
        foreach ($roles as $role) {
            if ($userRole === strtoupper($role)) {
                $allowed = true;
                break;
            }
        }

        if (!$allowed) {
            if ($request->expectsJson() || $request->ajax() || $request->is('siswa/api/*') || $request->is('api/*') || $request->is('*/api/*')) {
                return response()->json([
                    'status' => 'unauthorized',
                    'message' => 'Akses ditolak. Anda tidak memiliki wewenang untuk melakukan aksi ini.'
                ], 403);
            }
            return redirect()->route('login')->with('error', 'Akses ditolak. Peran Anda tidak diperbolehkan mengakses halaman tersebut.');
        }

        return $next($request);
    }
}
