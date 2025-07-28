# Configuración de Documentación

## Swagger

- Configuración en `config/l5-swagger.php`
- Variables de entorno en `.env` (`L5_SWAGGER_CONST_HOST`)
- Rutas personalizadas en `routes/web.php`

## Estructura de carpetas

- `/docs/api` para documentación de endpoints
- `/docs/swagger` para documentación técnica de Swagger

## Comandos útiles

```bash
php artisan l5-swagger:generate
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

## Recomendaciones

- Mantén la documentación actualizada tras cada cambio relevante en la API.
- Usa la interfaz Swagger UI para validar y probar los endpoints.
