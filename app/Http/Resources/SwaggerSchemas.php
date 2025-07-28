<?php

namespace App\Http\Resources;

/**
 * @OA\Schema(
 *      schema="User",
 *      type="object",
 *      title="Usuario",
 *      description="Modelo de usuario",
 *      required={"id", "name", "email", "created_at", "updated_at"},
 *      @OA\Property(
 *          property="id",
 *          type="integer",
 *          description="ID único del usuario",
 *          example=1
 *      ),
 *      @OA\Property(
 *          property="name",
 *          type="string",
 *          description="Nombre completo del usuario",
 *          example="Juan Pérez"
 *      ),
 *      @OA\Property(
 *          property="email",
 *          type="string",
 *          format="email",
 *          description="Correo electrónico del usuario",
 *          example="juan@ejemplo.com"
 *      ),
 *      @OA\Property(
 *          property="email_verified_at",
 *          type="string",
 *          format="date-time",
 *          description="Fecha de verificación del email",
 *          example="2025-07-16T12:00:00.000000Z",
 *          nullable=true
 *      ),
 *      @OA\Property(
 *          property="created_at",
 *          type="string",
 *          format="date-time",
 *          description="Fecha de creación",
 *          example="2025-07-16T12:00:00.000000Z"
 *      ),
 *      @OA\Property(
 *          property="updated_at",
 *          type="string",
 *          format="date-time",
 *          description="Fecha de última actualización",
 *          example="2025-07-16T12:00:00.000000Z"
 *      )
 * )
 *
 * @OA\Schema(
 *      schema="UserRequest",
 *      type="object",
 *      title="Solicitud de Usuario",
 *      description="Datos requeridos para crear un usuario",
 *      required={"name", "email", "password", "password_confirmation"},
 *      @OA\Property(
 *          property="name",
 *          type="string",
 *          description="Nombre completo del usuario",
 *          example="Juan Pérez"
 *      ),
 *      @OA\Property(
 *          property="email",
 *          type="string",
 *          format="email",
 *          description="Correo electrónico del usuario",
 *          example="juan@ejemplo.com"
 *      ),
 *      @OA\Property(
 *          property="password",
 *          type="string",
 *          format="password",
 *          description="Contraseña del usuario (mínimo 8 caracteres)",
 *          example="password123"
 *      ),
 *      @OA\Property(
 *          property="password_confirmation",
 *          type="string",
 *          format="password",
 *          description="Confirmación de la contraseña",
 *          example="password123"
 *      )
 * )
 *
 * @OA\Schema(
 *      schema="UserUpdateRequest",
 *      type="object",
 *      title="Actualización de Usuario",
 *      description="Datos para actualizar un usuario",
 *      @OA\Property(
 *          property="name",
 *          type="string",
 *          description="Nombre completo del usuario",
 *          example="Juan Carlos Pérez"
 *      ),
 *      @OA\Property(
 *          property="email",
 *          type="string",
 *          format="email",
 *          description="Correo electrónico del usuario",
 *          example="juancarlos@ejemplo.com"
 *      ),
 *      @OA\Property(
 *          property="password",
 *          type="string",
 *          format="password",
 *          description="Nueva contraseña (mínimo 8 caracteres)",
 *          example="newpassword123"
 *      ),
 *      @OA\Property(
 *          property="password_confirmation",
 *          type="string",
 *          format="password",
 *          description="Confirmación de la nueva contraseña",
 *          example="newpassword123"
 *      )
 * )
 *
 * @OA\Schema(
 *      schema="ApiResponse",
 *      type="object",
 *      title="Respuesta de API",
 *      description="Estructura estándar de respuesta de la API",
 *      @OA\Property(
 *          property="success",
 *          type="boolean",
 *          description="Indica si la operación fue exitosa",
 *          example=true
 *      ),
 *      @OA\Property(
 *          property="message",
 *          type="string",
 *          description="Mensaje descriptivo de la operación",
 *          example="Operation completed successfully"
 *      ),
 *      @OA\Property(
 *          property="data",
 *          description="Datos de la respuesta (opcional)",
 *          nullable=true
 *      )
 * )
 *
 * @OA\Schema(
 *      schema="ValidationError",
 *      type="object",
 *      title="Error de Validación",
 *      description="Estructura de respuesta para errores de validación",
 *      @OA\Property(
 *          property="success",
 *          type="boolean",
 *          description="Indica si la operación fue exitosa",
 *          example=false
 *      ),
 *      @OA\Property(
 *          property="message",
 *          type="string",
 *          description="Mensaje de error",
 *          example="Validation failed"
 *      ),
 *      @OA\Property(
 *          property="errors",
 *          type="object",
 *          description="Detalles de los errores de validación",
 *          additionalProperties={
 *              "type": "array",
 *              "items": {
 *                  "type": "string"
 *              }
 *          }
 *      )
 * )
 */
class SwaggerSchemas
{
    // Esta clase solo contiene documentación para Swagger
}
