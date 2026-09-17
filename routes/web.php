<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
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

Route::get('/api-tester', function () {
    return Inertia::render('api-tester');
})->name('api.tester');

Route::post('/api-tester-proxy', function (Request $request) {
    $cards = $request->input('cards', []);

    if (!is_array($cards)) {
        $cards = [$cards];
    }

    $normalizedCards = [];
    foreach ($cards as $card) {
        $card = trim((string) $card);

        if ($card === '') {
            continue;
        }

        $normalizedCards[] = (int) $card;
    }

    if (empty($normalizedCards)) {
        return response()->json([
            'success' => false,
            'message' => 'Ingresa al menos un número de tarjeta.',
        ], 422);
    }

    try {
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post('http://172.16.10.21:8081/api/v1/cards/multiple', [
            'CARDS' => $normalizedCards,
        ]);

        $body = $response->json();

        if (!$response->successful()) {
            return response()->json([
                'success' => false,
                'message' => $body['message'] ?? 'La API respondió con error.',
                'error' => $body,
            ], $response->status());
        }

        return response()->json([
            'success' => true,
            'message' => 'Tarjetas consultadas correctamente.',
            'data' => $body['data'] ?? [],
            'total_found' => $body['total_found'] ?? 0,
            'total_requested' => $body['total_requested'] ?? count($normalizedCards),
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'No se pudo conectar con la API externa.',
            'error' => $e->getMessage(),
        ], 500);
    }
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
