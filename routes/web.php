<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

// Ruta directa para acceder a la documentación de la API
Route::get('/docs', function () {
    return view('swagger-ui');
})->name('api.docs');

// Ruta alternativa para la documentación
Route::get('/api/docs', function () {
    return view('swagger-ui');
})->name('api.documentation');

// Ruta para servir el archivo JSON de documentación
Route::get('/api-docs.json', function () {
    $path = storage_path('api-docs/api-docs.json');

    if (!file_exists($path)) {
        Artisan::call('l5-swagger:generate');
    }

    return response()->file($path, [
        'Content-Type' => 'application/json',
    ]);
})->name('api.json');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
