
# Upgrade Guide - Laravel Glide Enhanced

## Route Prefix Change (v2.x)

### ⚠️ Important Change: New Route Prefix

To avoid conflicts with other packages (especially `laravel-dropzone-enhanced`), we have changed the default route prefix from `/img/` to `/glide/`.

### What changed:

**Before:**
```
https://your-site.com/img/path/image.jpg
```

**Now:**
```
https://your-site.com/glide/path/image.jpg
```

### How to update:

#### Option 1: Use the new prefix (Recommended)
1. Publish the new configuration:
```bash
php artisan vendor:publish --provider="MacCesar\LaravelGlideEnhanced\ImageProcessorServiceProvider" --tag="config" --force
```

2. Clear cache:
```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

3. Update any hardcoded references in your code from `/img/` to `/glide/`

#### Option 2: Keep the previous prefix
If you prefer to keep the `/img/` prefix, you can modify your `config/images.php` file:

```php
'routes' => [
    'enabled' => true,
    'prefix' => 'img',  // Change from 'glide' to 'img'
    'middleware' => ['web'],
],
```

**Note:** If you use `laravel-dropzone-enhanced` together with this package, it is recommended to use the new `/glide/` prefix to avoid route conflicts.

### Compatibility with Laravel Dropzone Enhanced

If you install both packages:
- **Laravel Glide Enhanced** will use the `/glide/` prefix to generate images dynamically
- **Laravel Dropzone Enhanced** will use the `/dropzone/` prefix for its functionalities
- The `Photo` model methods will automatically detect and use Laravel Glide Enhanced when available

### Verification

After the update, verify that the routes are registered correctly:

```bash
php artisan route:list | grep glide
```

You should see something like:
```
GET|HEAD  glide/{path} ... images.show
```
