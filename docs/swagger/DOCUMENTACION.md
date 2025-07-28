# Documentación de API con Swagger

## 🚀 Configuración Completada

Se ha configurado exitosamente **L5-Swagger** para documentar las APIs de usuarios. La documentación está disponible en:

```
http://localhost:8000/api/documentation
```

## 📁 Estructura de Archivos Swagger

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/
│   │   │   └── ApiDocumentationController.php (Configuración base)
│   │   └── User/
│   │       └── UserController.php (Anotaciones completas)
│   └── Resources/
│       └── SwaggerSchemas.php (Esquemas de datos)
├── Providers/
│   └── AppServiceProvider.php (Configuración de URL)
config/
└── l5-swagger.php (Configuración principal)
```

## 🔧 Características Implementadas

### 1. **Documentación Completa de Endpoints**

- ✅ **GET /api/v1/users** - Listar usuarios con filtros
- ✅ **POST /api/v1/users** - Crear nuevos usuarios
- ✅ **GET /api/v1/users/{id}** - Obtener usuario específico
- ✅ **PUT/PATCH /api/v1/users/{id}** - Actualizar usuario
- ✅ **DELETE /api/v1/users/{id}** - Eliminar usuario

### 2. **Esquemas de Datos**

- ✅ **User Schema** - Modelo de usuario completo
- ✅ **UserRequest Schema** - Datos para crear usuario
- ✅ **UserUpdateRequest Schema** - Datos para actualizar usuario
- ✅ **ApiResponse Schema** - Respuesta estándar de API
- ✅ **ValidationError Schema** - Errores de validación

### 3. **Documentación Detallada**

- ✅ Parámetros de entrada con tipos y validaciones
- ✅ Respuestas de éxito y error
- ✅ Códigos de estado HTTP
- ✅ Ejemplos de respuesta JSON
- ✅ Descripciones en español

## 📋 Cómo Usar la Documentación

### **Acceder a la Documentación**

1. Ejecuta el servidor: `php artisan serve`
2. Visita: `http://localhost:8000/api/documentation`
3. Explora los endpoints disponibles

### **Probar APIs desde Swagger**

1. Abre la documentación en el navegador
2. Selecciona un endpoint (ej: POST /api/v1/users)
3. Haz clic en "Try it out"
4. Completa los datos requeridos
5. Haz clic en "Execute"
6. Revisa la respuesta

### **Ejemplo de Uso: Crear Usuario**

```json
{
    "name": "Juan Pérez",
    "email": "juan@ejemplo.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

## 🔄 Regenerar Documentación

Después de hacer cambios en las anotaciones, ejecuta:

```bash
php artisan l5-swagger:generate
```

## 📝 Comandos Útiles

```bash
# Generar documentación
php artisan l5-swagger:generate

# Limpiar documentación
php artisan l5-swagger:generate --force

# Ver rutas de documentación
php artisan route:list --name=l5-swagger
```

## 🎯 Beneficios de Swagger

### **Para Desarrolladores**

- ✅ Documentación automática y actualizada
- ✅ Interfaz interactiva para pruebas
- ✅ Validación de tipos de datos
- ✅ Ejemplos de uso integrados

### **Para el Equipo**

- ✅ Documentación siempre actualizada
- ✅ Estándar profesional de documentación
- ✅ Facilita la colaboración
- ✅ Reduce errores de integración

### **Para el Cliente/Frontend**

- ✅ Comprensión clara de la API
- ✅ Ejemplos de requests/responses
- ✅ Códigos de error documentados
- ✅ Interfaz amigable para pruebas

## 🔧 Configuración Avanzada

### **Personalizar la Documentación**

Edita `config/l5-swagger.php` para:

- Cambiar rutas de acceso
- Personalizar el título y descripción
- Configurar autenticación
- Agregar información de contacto

### **Agregar Autenticación**

```php
// En ApiDocumentationController.php
/**
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
```

### **Documentar Nuevos Endpoints**

1. Agrega anotaciones `@OA\` en el controlador
2. Define esquemas en `SwaggerSchemas.php`
3. Regenera la documentación

## 📊 Estructura de Respuestas

### **Respuesta Exitosa**

```json
{
  "success": true,
  "message": "Operation completed successfully",
  "data": { ... }
}
```

### **Error de Validación**

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

### **Error del Servidor**

```json
{
    "success": false,
    "message": "Error message",
    "error": "Detailed error information"
}
```

## 🚀 Próximos Pasos

1. **Agregar autenticación** con Sanctum
2. **Documentar más módulos** (productos, pedidos, etc.)
3. **Implementar versionado** de API
4. **Agregar tests automáticos** basados en Swagger
5. **Configurar CI/CD** para regenerar docs automáticamente

---

## 🎉 ¡Documentación Lista!

Tu API de usuarios ahora tiene documentación profesional con Swagger. Visita `http://localhost:8000/api/documentation` para ver el resultado final.

**Características implementadas:**

- ✅ Documentación completa de endpoints
- ✅ Esquemas de datos definidos
- ✅ Interfaz interactiva
- ✅ Ejemplos de uso
- ✅ Validaciones documentadas
- ✅ Respuestas de error detalladas
