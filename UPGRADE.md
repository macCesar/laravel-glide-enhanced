
# Upgrade Guide - Laravel Glide Enhanced

## Upgrading to v3

Version 3 is a breaking security and platform release:

- PHP 8.2+ and Laravel 12 or 13 are required. Laravel 8–11 are no longer supported.
- Move source images from the disk root to `<disk>/images/`, or set
  `images.source_root` to a dedicated existing directory.
- Move watermarks to `<disk>/watermarks/`, or set `images.watermark_root`.
- Only decodable JPEG, PNG, and WebP files are accepted by default.
- Width and height are capped at 4096, output cost at 16 megapixels, and DPR at 4.
- Unknown or invalid Glide query parameters now return HTTP 422.
- The public route uses `web` and `throttle:60,1` by default.
- HTTP cache headers are configurable at `images.cache.headers`; responses include
  ETag, Last-Modified, and `X-Content-Type-Options: nosniff`.
- Cache cleanup now reads `images.cache.lifetime`. Explicit `--days=0` still
  deletes the entire generated cache.

Publish the new configuration and review it before clearing the old cache:

```bash
php artisan vendor:publish --tag=images-config --force
php artisan images:clean-cache --days=0
php artisan config:clear
php artisan route:clear
```

Public URLs retain the `/glide/{path}?w=...` shape; the new source root is an
internal filesystem boundary and does not appear in URLs.

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
