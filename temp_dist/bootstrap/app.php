<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Bebaskan route webhook dari proteksi CSRF
        $middleware->validateCsrfTokens(except: [
            'api/webhook/clear-cache',
        ]);

        // Cegah pemotongan spasi (trimming) pada parameter pencarian jadwal Google Sheets
        $middleware->trimStrings(except: [
            'original_tanggal',
            'original_jam',
            'original_dosen',
            'tanggal',
            'jam',
            'dosen',
        ]);

        // Daftarkan alias middleware custom lu di sini
        $middleware->alias([
            'cek.login' => \App\Http\Middleware\CekLogin::class,
            'cek.role'  => \App\Http\Middleware\CheckRole::class,
        ]);

        $middleware->redirectGuestsTo(function (\Illuminate\Http\Request $request) {
            if ($request->is('admin') || $request->is('admin/*')) {
                return route('admin.login');
            }
            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();