# API de Usuarios - Documentación

## Endpoints Disponibles

### 1. Obtener todos los usuarios (GET)

```
GET /api/v1/users
```

**Parámetros de consulta opcionales:**

- `name`: Filtrar por nombre (búsqueda parcial)
- `email`: Filtrar por email (búsqueda parcial)
- `per_page`: Número de usuarios por página (default: 10)
- `page`: Número de página

**Ejemplo de request:**

```bash
curl -X GET "http://localhost:8000/api/v1/users?name=john&per_page=5" \
  -H "Accept: application/json"
```

**Ejemplo de respuesta:**

```json
{
  "success": true,
  "message": "Users retrieved successfully",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "email_verified_at": null,
        "created_at": "2025-07-16T12:00:00.000000Z",
        "updated_at": "2025-07-16T12:00:00.000000Z"
      }
    ],
    "first_page_url": "http://localhost:8000/api/v1/users?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http://localhost:8000/api/v1/users?page=1",
    "links": [...],
    "next_page_url": null,
    "path": "http://localhost:8000/api/v1/users",
    "per_page": 10,
    "prev_page_url": null,
    "to": 1,
    "total": 1
  }
}
```

### 2. Obtener un usuario específico (GET)

```
GET /api/v1/users/{id}
```

**Ejemplo de request:**

```bash
curl -X GET "http://localhost:8000/api/v1/users/1" \
  -H "Accept: application/json"
```

**Ejemplo de respuesta:**

```json
{
    "success": true,
    "message": "User retrieved successfully",
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "email_verified_at": null,
        "created_at": "2025-07-16T12:00:00.000000Z",
        "updated_at": "2025-07-16T12:00:00.000000Z"
    }
}
```

### 3. Crear un nuevo usuario (POST)

```
POST /api/v1/users
```

**Campos requeridos:**

- `name`: Nombre del usuario (string, máximo 255 caracteres)
- `email`: Email del usuario (string, email válido, único)
- `password`: Contraseña (string, mínimo 8 caracteres)
- `password_confirmation`: Confirmación de contraseña (debe coincidir con password)

**Ejemplo de request:**

```bash
curl -X POST "http://localhost:8000/api/v1/users" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Jane Doe",
    "email": "jane@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

**Ejemplo de respuesta exitosa:**

```json
{
    "success": true,
    "message": "User created successfully",
    "data": {
        "id": 2,
        "name": "Jane Doe",
        "email": "jane@example.com",
        "email_verified_at": null,
        "created_at": "2025-07-16T12:00:00.000000Z",
        "updated_at": "2025-07-16T12:00:00.000000Z"
    }
}
```

**Ejemplo de respuesta con errores de validación:**

```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "email": ["Este correo electrónico ya está registrado"],
        "password": ["La contraseña debe tener al menos 8 caracteres"]
    }
}
```

### 4. Actualizar un usuario (PUT/PATCH)

```
PUT /api/v1/users/{id}
PATCH /api/v1/users/{id}
```

**Campos opcionales:**

- `name`: Nuevo nombre
- `email`: Nuevo email (debe ser único)
- `password`: Nueva contraseña (mínimo 8 caracteres)
- `password_confirmation`: Confirmación de nueva contraseña

**Ejemplo de request:**

```bash
curl -X PUT "http://localhost:8000/api/v1/users/1" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Smith",
    "email": "johnsmith@example.com"
  }'
```

### 5. Eliminar un usuario (DELETE)

```
DELETE /api/v1/users/{id}
```

**Ejemplo de request:**

```bash
curl -X DELETE "http://localhost:8000/api/v1/users/1" \
  -H "Accept: application/json"
```

**Ejemplo de respuesta:**

```json
{
    "success": true,
    "message": "User deleted successfully"
}
```

## Códigos de Estado HTTP

- `200 OK`: Operación exitosa
- `201 Created`: Usuario creado exitosamente
- `404 Not Found`: Usuario no encontrado
- `422 Unprocessable Entity`: Errores de validación
- `500 Internal Server Error`: Error del servidor

## Notas importantes

1. **Validación**: Todos los campos son validados en el servidor
2. **Paginación**: Los listados están paginados por defecto
3. **Filtros**: Se pueden aplicar filtros en el listado de usuarios
4. **Seguridad**: Las contraseñas se encriptan automáticamente
5. **Respuestas**: Todas las respuestas siguen el mismo formato JSON

## Ejecutar las APIs

Para probar las APIs, asegúrate de que el servidor esté ejecutándose:

```bash
php artisan serve
```

Las APIs estarán disponibles en `http://localhost:8000/api/v1/users`
