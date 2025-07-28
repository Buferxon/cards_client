<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Laravel Cards Client API",
 *      description="Documentación de la API para el sistema de gestión de usuarios",
 *      @OA\Contact(
 *          email="admin@example.com"
 *      ),
 *      @OA\License(
 *          name="MIT",
 *          url="https://opensource.org/licenses/MIT"
 *      )
 * )
 *
 * @OA\Server(
 *      url="http://localhost:8081",
 *      description="Servidor de desarrollo"
 * )
 * @OA\Server(
 *     url="http://172.16.10.21:8081",
 *     description="Servidor producción"
 * )
 *
 * @OA\PathItem(
 *      path="/api/v1"
 * )
 *
 * @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="JWT"
 * )
 *
 * @OA\Tag(
 *      name="Users",
 *      description="Operaciones relacionadas con usuarios"
 * )
 */
class ApiDocumentationController extends Controller
{
    //
}
