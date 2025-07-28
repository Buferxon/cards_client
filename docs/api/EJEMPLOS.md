# Ejemplos de Uso - API de Usuarios

## Configuración del Entorno

Asegúrate de que el servidor esté ejecutándose:

```bash
php artisan serve
```

## Ejemplos con cURL

### 1. Crear un Usuario (POST)

```bash
curl -X POST "http://localhost:8000/api/v1/users" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Juan Pérez",
    "email": "juan@ejemplo.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

**Respuesta exitosa:**

```json
{
    "success": true,
    "message": "User created successfully",
    "data": {
        "id": 1,
        "name": "Juan Pérez",
        "email": "juan@ejemplo.com",
        "email_verified_at": null,
        "created_at": "2025-07-16T12:00:00.000000Z",
        "updated_at": "2025-07-16T12:00:00.000000Z"
    }
}
```

### 2. Obtener Todos los Usuarios (GET)

```bash
curl -X GET "http://localhost:8000/api/v1/users" \
  -H "Accept: application/json"
```

**Con filtros:**

```bash
curl -X GET "http://localhost:8000/api/v1/users?name=Juan&per_page=5" \
  -H "Accept: application/json"
```

### 3. Obtener un Usuario Específico (GET)

```bash
curl -X GET "http://localhost:8000/api/v1/users/1" \
  -H "Accept: application/json"
```

### 4. Actualizar un Usuario (PUT)

```bash
curl -X PUT "http://localhost:8000/api/v1/users/1" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Juan Carlos Pérez",
    "email": "juancarlos@ejemplo.com"
  }'
```

### 5. Eliminar un Usuario (DELETE)

```bash
curl -X DELETE "http://localhost:8000/api/v1/users/1" \
  -H "Accept: application/json"
```

## Ejemplos con JavaScript/Fetch

### Crear Usuario

```javascript
fetch('http://localhost:8000/api/v1/users', {
    method: 'POST',
    headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        name: 'María González',
        email: 'maria@ejemplo.com',
        password: 'password123',
        password_confirmation: 'password123',
    }),
})
    .then((response) => response.json())
    .then((data) => console.log(data));
```

### Obtener Usuarios

```javascript
fetch('http://localhost:8000/api/v1/users?page=1&per_page=10')
    .then((response) => response.json())
    .then((data) => console.log(data));
```

### Actualizar Usuario

```javascript
fetch('http://localhost:8000/api/v1/users/1', {
    method: 'PUT',
    headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        name: 'María Isabel González',
    }),
})
    .then((response) => response.json())
    .then((data) => console.log(data));
```

## Ejemplos con Postman

### Collection JSON para importar:

```json
{
    "info": {
        "name": "Laravel Cards Client - Users API",
        "description": "Colección de endpoints para la API de usuarios"
    },
    "item": [
        {
            "name": "Create User",
            "request": {
                "method": "POST",
                "url": "http://localhost:8000/api/v1/users",
                "header": [
                    {
                        "key": "Accept",
                        "value": "application/json"
                    },
                    {
                        "key": "Content-Type",
                        "value": "application/json"
                    }
                ],
                "body": {
                    "mode": "raw",
                    "raw": "{\n  \"name\": \"Test User\",\n  \"email\": \"test@example.com\",\n  \"password\": \"password123\",\n  \"password_confirmation\": \"password123\"\n}"
                }
            }
        },
        {
            "name": "Get All Users",
            "request": {
                "method": "GET",
                "url": "http://localhost:8000/api/v1/users",
                "header": [
                    {
                        "key": "Accept",
                        "value": "application/json"
                    }
                ]
            }
        },
        {
            "name": "Get User by ID",
            "request": {
                "method": "GET",
                "url": "http://localhost:8000/api/v1/users/1",
                "header": [
                    {
                        "key": "Accept",
                        "value": "application/json"
                    }
                ]
            }
        },
        {
            "name": "Update User",
            "request": {
                "method": "PUT",
                "url": "http://localhost:8000/api/v1/users/1",
                "header": [
                    {
                        "key": "Accept",
                        "value": "application/json"
                    },
                    {
                        "key": "Content-Type",
                        "value": "application/json"
                    }
                ],
                "body": {
                    "mode": "raw",
                    "raw": "{\n  \"name\": \"Updated User Name\"\n}"
                }
            }
        },
        {
            "name": "Delete User",
            "request": {
                "method": "DELETE",
                "url": "http://localhost:8000/api/v1/users/1",
                "header": [
                    {
                        "key": "Accept",
                        "value": "application/json"
                    }
                ]
            }
        }
    ]
}
```

## Manejo de Errores

### Error de Validación (422)

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

### Usuario No Encontrado (404)

```json
{
    "success": false,
    "message": "User not found"
}
```

### Error del Servidor (500)

```json
{
    "success": false,
    "message": "Error creating user",
    "error": "Database connection failed"
}
```
