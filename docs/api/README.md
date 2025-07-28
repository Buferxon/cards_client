# Documentación de la API

Esta sección contiene la documentación funcional de los endpoints RESTful del módulo de usuarios.

## Endpoints principales

- `GET /api/v1/users` - Listar usuarios
- `POST /api/v1/users` - Crear usuario
- `GET /api/v1/users/{id}` - Mostrar usuario
- `PUT/PATCH /api/v1/users/{id}` - Actualizar usuario
- `DELETE /api/v1/users/{id}` - Eliminar usuario

## Ejemplo de uso

```bash
curl -X GET http://localhost:8000/api/v1/users
```

## Referencias

- [Controlador](../../app/Http/Controllers/User/UserController.php)
- [Requests](../../app/Http/Requests/User/StoreUserRequest.php)

---

Consulta la documentación Swagger para detalles técnicos y pruebas interactivas.
