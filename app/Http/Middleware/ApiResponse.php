<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Establecer headers para API
        $request->headers->set('Accept', 'application/json');

        $response = $next($request);

        // Asegurar que la respuesta sea JSON
        if ($response->headers->get('Content-Type') !== 'application/json') {
            $response->headers->set('Content-Type', 'application/json');
        }

        return $response;
    }
}
