<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

// --- RETASAN KHUSUS UNTUK VERCEL ---
// Jika aplikasi mendeteksi sedang berjalan di Vercel...
if (isset($_SERVER['VERCEL']) || isset($_ENV['VERCEL'])) {
    $storagePath = '/tmp/storage';
    
    // 1. Paksa Laravel menggunakan /tmp sebagai folder storage utamanya
    $app->useStoragePath($storagePath);

    // 2. Buat sub-folder secara otomatis agar Livewire tidak panik
    $directories = [
        $storagePath . '/framework/views',
        $storagePath . '/framework/cache',
        $storagePath . '/framework/cache/data',
        $storagePath . '/framework/sessions',
        $storagePath . '/logs',
    ];

    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }
}
// -----------------------------------

return $app;