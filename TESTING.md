# Pruebas de Compatibilidad entre Laravel Glide Enhanced y Laravel Dropzone Enhanced

## Tests a realizar después de la actualización

### 1. Verificar rutas registradas

```bash
php artisan route:list | grep -E "(glide|dropzone)"
```

**Resultado esperado:**
```
GET|HEAD  glide/{path}               ... images.show
POST      dropzone/upload            ... dropzone.upload
DELETE    dropzone/photos/{id}       ... dropzone.destroy
POST      dropzone/photos/{id}/main  ... dropzone.setMain
GET|HEAD  dropzone/photos/{id}/is-main ... dropzone.checkIsMain
POST      dropzone/photos/reorder    ... dropzone.reorder
GET|HEAD  dropzone/image/{path}      ... dropzone.image
```

### 2. Verificar configuraciones

```bash
php artisan config:show images.routes.prefix
php artisan config:show dropzone.storage.disk
```

**Resultado esperado:**
- `images.routes.prefix` = "glide"
- `dropzone.storage.disk` = "public"

### 3. Limpiar caché

```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

### 4. Pruebas funcionales

#### Crear un modelo de prueba:

```php
// En tinker o un controlador de prueba
$photo = new \MacCesar\LaravelDropzoneEnhanced\Models\Photo([
    'filename' => 'test.jpg',
    'directory' => 'images',
    'disk' => 'public',
    'extension' => 'jpg',
]);

// Probar URL normal
echo $photo->getUrl();
// Debería mostrar: http://tu-sitio.com/glide/images/test.jpg

// Probar URL thumbnail
echo $photo->getThumbnailUrl('200x200');
// Debería mostrar: http://tu-sitio.com/glide/images/test.jpg?w=200&h=200&fit=crop&q=90
```

#### Probar URLs directamente:

1. **Laravel Glide Enhanced:**
   - `http://tu-sitio.com/glide/storage/path/image.jpg`

2. **Laravel Dropzone Enhanced:**
   - `http://tu-sitio.com/dropzone/image/storage/path/image.jpg`

### 5. Verificar que no hay conflictos

Las siguientes URLs deben funcionar independientemente:

- `/glide/` → Laravel Glide Enhanced (procesamiento dinámico)
- `/dropzone/` → Laravel Dropzone Enhanced (gestión de archivos)
- `/storage/` → Laravel nativo (acceso directo a archivos)

### 6. Test de integración

```php
// Verificar que Photo model puede usar Laravel Glide Enhanced
$hasGlide = class_exists('MacCesar\LaravelGlideEnhanced\Facades\ImageProcessor');
echo $hasGlide ? "✅ Laravel Glide Enhanced detectado" : "❌ Laravel Glide Enhanced NO detectado";

// Verificar que las URLs se generan correctamente
$photo = new \MacCesar\LaravelDropzoneEnhanced\Models\Photo([
    'filename' => 'test.jpg',
    'directory' => 'images',
    'disk' => 'public',
]);

$url = $photo->getUrl();
$containsGlide = str_contains($url, '/glide/');
echo $containsGlide ? "✅ URLs usan prefijo /glide/" : "❌ URLs NO usan prefijo /glide/";
```

### 7. Verificar logs

Si algo falla, revisar:

```bash
tail -f storage/logs/laravel.log
```

Buscar errores relacionados con:
- Rutas duplicadas
- Conflictos de namespace
- Problemas de acceso a archivos

### Problemas comunes y soluciones

#### Problema: "Route not found" para imágenes
**Solución:** Verificar que el prefijo esté configurado correctamente y limpiar caché de rutas.

#### Problema: Imágenes no se procesan dinámicamente
**Solución:** Verificar que `config('images.routes.enabled')` sea `true`.

#### Problema: Conflictos entre paquetes
**Solución:** Asegurarse de que ambos paquetes usen prefijos diferentes (`glide` vs `dropzone`).
