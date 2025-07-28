# Estructura de Módulos - Laravel Cards Client

## Organización por Módulos

La aplicación está organizada por módulos para mantener el código limpio y escalable. Cada módulo tiene sus propios Controllers, Requests, y otros archivos relacionados.

## Estructura de Carpetas

```
app/
├── Http/
│   ├── Controllers/
│   │   └── User/
│   │       └── UserController.php
│   ├── Requests/
│   │   └── User/
│   │       ├── StoreUserRequest.php
│   │       └── UpdateUserRequest.php
│   └── Middleware/
│       └── ApiResponse.php
├── Models/
│   └── User.php
└── ...
```

## Módulo de Usuarios

### Controlador

- **Ubicación**: `app/Http/Controllers/User/UserController.php`
- **Namespace**: `App\Http\Controllers\User`
- **Funciones**:
    - `index()` - Listar usuarios con filtros y paginación
    - `store()` - Crear nuevo usuario
    - `show()` - Mostrar usuario específico
    - `update()` - Actualizar usuario
    - `destroy()` - Eliminar usuario

### Requests de Validación

- **StoreUserRequest**: `app/Http/Requests/User/StoreUserRequest.php`

    - Validaciones para crear usuarios
    - Mensajes de error personalizados en español
    - Manejo de errores de validación en formato JSON

- **UpdateUserRequest**: `app/Http/Requests/User/UpdateUserRequest.php`
    - Validaciones para actualizar usuarios
    - Reglas condicionales (sometimes)
    - Validación de email único excluyendo el usuario actual

### Rutas API

- **Archivo**: `routes/api.php`
- **Prefix**: `/api/v1`
- **Endpoints**:
    - `GET /api/v1/users` - Listar usuarios
    - `POST /api/v1/users` - Crear usuario
    - `GET /api/v1/users/{id}` - Mostrar usuario
    - `PUT/PATCH /api/v1/users/{id}` - Actualizar usuario
    - `DELETE /api/v1/users/{id}` - Eliminar usuario

## Ventajas de esta Estructura

1. **Organización**: Cada módulo tiene sus archivos relacionados agrupados
2. **Escalabilidad**: Fácil agregar nuevos módulos
3. **Mantenibilidad**: Código más limpio y fácil de mantener
4. **Separación de responsabilidades**: Cada clase tiene una función específica
5. **Reutilización**: Los requests pueden ser reutilizados en diferentes contextos

## Agregar Nuevos Módulos

Para agregar un nuevo módulo (por ejemplo, "Product"), sigue esta estructura:

```
app/Http/Controllers/Product/
├── ProductController.php
└── ProductApiController.php (opcional)

app/Http/Requests/Product/
├── StoreProductRequest.php
├── UpdateProductRequest.php
└── DeleteProductRequest.php (opcional)
```

## Middleware

- **ApiResponse**: Middleware personalizado para asegurar respuestas JSON consistentes
- **Ubicación**: `app/Http/Middleware/ApiResponse.php`

## Modelo

- **User**: Modelo principal con scopes y relaciones
- **Ubicación**: `app/Models/User.php`
- **Funciones adicionales**:
    - `scopeByName()` - Filtrar por nombre
    - `scopeByEmail()` - Filtrar por email

## Próximos Pasos

1. Agregar autenticación API con Sanctum
2. Implementar roles y permisos
3. Crear tests para los endpoints
4. Agregar más módulos según sea necesario
5. Implementar cache para mejorar performance
