<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

class SwaggerController extends Controller
{
    public function docs()
    {
        // Verificar si el archivo de documentación existe
        if (!Storage::disk('local')->exists('api-docs/api-docs.json')) {
            // Generar documentación si no existe
            Artisan::call('l5-swagger:generate');
        }

        // Obtener el contenido del archivo JSON
        $jsonContent = Storage::disk('local')->get('api-docs/api-docs.json');

        // Crear la página HTML con Swagger UI
        return view('swagger-ui', compact('jsonContent'));
    }

    public function json()
    {
        // Verificar si el archivo existe
        if (!Storage::disk('local')->exists('api-docs/api-docs.json')) {
            Artisan::call('l5-swagger:generate');
        }

        $jsonContent = Storage::disk('local')->get('api-docs/api-docs.json');

        return response($jsonContent, 200, [
            'Content-Type' => 'application/json',
        ]);
    }
}
