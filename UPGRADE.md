# Guía de Actualización - Laravel Glide Enhanced

## Cambio de Prefijo de Rutas (v2.x)

### ⚠️ Cambio Importante: Nuevo Prefijo de Rutas

Para evitar conflictos con otros paquetes (especialmente `laravel-dropzone-enhanced`), hemos cambiado el prefijo por defecto de las rutas de `/img/` a `/glide/`.

### Lo que cambió:

**Antes:**
```
http://tu-sitio.com/img/ruta/imagen.jpg
```

**Ahora:**
```
http://tu-sitio.com/glide/ruta/imagen.jpg
```

### Cómo actualizar:

#### Opción 1: Usar el nuevo prefijo (Recomendado)
1. Publicar la nueva configuración:
```bash
php artisan vendor:publish --provider="MacCesar\LaravelGlideEnhanced\ImageProcessorServiceProvider" --tag="config" --force
```

2. Limpiar caché:
```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

3. Actualizar cualquier referencia hardcodeada en tu código de `/img/` a `/glide/`

#### Opción 2: Mantener el prefijo anterior
Si prefieres mantener el prefijo `/img/`, puedes modificar tu archivo `config/images.php`:

```php
'routes' => [
    'enabled' => true,
    'prefix' => 'img',  // Cambiar de 'glide' a 'img'
    'middleware' => ['web'],
],
```

**Nota:** Si usas `laravel-dropzone-enhanced` junto con este paquete, se recomienda usar el nuevo prefijo `/glide/` para evitar conflictos de rutas.

### Compatibilidad con Laravel Dropzone Enhanced

Si instalas ambos paquetes:
- **Laravel Glide Enhanced** usará el prefijo `/glide/` para generar imágenes dinámicamente
- **Laravel Dropzone Enhanced** usará el prefijo `/dropzone/` para sus funcionalidades
- Los métodos del modelo `Photo` automáticamente detectarán y usarán Laravel Glide Enhanced cuando esté disponible

### Verificación

Después de la actualización, verifica que las rutas estén registradas correctamente:

```bash
php artisan route:list | grep glide
```

Deberías ver algo como:
```
GET|HEAD  glide/{path} ... images.show
```
