# Image Processing System Manual

This manual describes the image processing system implemented in this Laravel application, based on [Spatie/Laravel-Glide](https://github.com/spatie/laravel-glide).

## Table of Contents

1. [Introduction](#introduction)
2. [Configuration](#configuration)
3. [Basic Usage](#basic-usage)
4. [HasImages Trait](#hasimages-trait)
5. [Processing Parameters](#processing-parameters)
6. [Practical Examples](#practical-examples)
7. [WebP Optimization](#webp-optimization)
8. [Image Presets](#image-presets)
9. [Default Images](#default-images)
10. [Cache Maintenance](#cache-maintenance)
11. [Advanced Usage](#advanced-usage)

## Introduction

The image processing system allows:

- Dynamically resizing and cropping images
- Converting images to different formats (JPG, PNG, WebP)
- Applying filters and effects
- Optimizing images for the web
- Caching processed images to improve performance

## Configuration

### Configuration File

The system uses a configuration file `config/images.php` that allows customizing various aspects:

```php
// Publish configuration file (run once)
php artisan vendor:publish --tag=images-config
```

The file contains the following main sections:

- **defaults**: Default image configuration for different categories
- **cache**: Cache system configuration
- **presets**: Predefined presets for image manipulation

### Main Route

The system uses the following route to process images:

```php
Route::get('/img/{path}', [ImageController::class, 'show'])->where('path', '.*');
```

This route captures any request that starts with `/img/` and redirects it to the image controller.

### File Structure

- `app/Http/Controllers/ImageController.php`: Main controller for image processing
- `app/Traits/HasImages.php`: Trait to facilitate use in models
- `app/Console/Commands/CleanImageCache.php`: Command for cache cleaning
- `config/images.php`: Configuration file
- `routes/web.php`: Contains the route definition

## Basic Usage

To display a processed image, use the following URL format:

```
/img/storage/path/to/image.jpg?w=300&h=200&fit=crop
```

Where:
- `/img/`: Route prefix for processing
- `storage/path/to/image.jpg`: Path to the image in storage
- `?w=300&h=200&fit=crop`: Processing parameters

## HasImages Trait

The `HasImages` trait provides useful methods to generate processed image URLs directly from models.

### Main Methods

```php
// Generate processed image URL
$model->fotoUrl($dimensions, $format, $watermark, $extraParams);

// Generate responsive WebP image URL
$model->webpUrl($width, $height, $fit, $quality);

// Use a predefined preset
$model->imagePreset('thumbnail'); // 150x150 WebP
$model->imagePreset('social');    // 1200x630 JPG
```

### Usage Example

```php
// 300x200 pixels thumbnail
$producto->fotoUrl('300x200');

// 600px wide optimized WebP image
$producto->webpUrl(600);

// Image with watermark
$producto->fotoUrl('800x600', 'jpg', 'watermarks/logo.png');

// Use a predefined preset
$producto->imagePreset('thumbnail'); // 150x150 WebP
$producto->imagePreset('social');    // 1200x630 JPG
```

### Customization in Models

To customize behavior, you can override these methods in your models:

```php
protected function getFotoPath()
{
  // Customize the image path
  return $this->custom_path;
}

protected function getDefaultImageUrl()
{
  // Customize the default image
  return asset('img/product-default.jpg');
}
```

## Processing Parameters

The system supports all parameters from the Glide API for image processing:

### Basic Size Adjustments

- `w` - Width (pixels)
- `h` - Height (pixels)
- `fit` - Fit method (crop, contain, max, fill)
- `dpr` - Device pixel ratio (1, 2, 3, etc.)

### Format and Quality Adjustments

- `q` - Image quality (0-100)
- `fm` - Output format (jpg, png, gif, webp)
- `bg` - Background color (hex, rgb, rgba)

### Effects and Filters

- `filt` - Predefined filters (greyscale, sepia)
- `blur` - Blur (0-100)
- `bri` - Brightness (-100 to 100)
- `con` - Contrast (-100 to 100)
- `gam` - Gamma (0.1-9.99)
- `sharp` - Sharpness (0-100)

### Cropping and Orientation

- `crop` - Manual crop (width,height,x,y)
- `or` - Orientation (auto, 0, 90, 180, 270)
- `flip` - Flip (v, h, both)

## Practical Examples

### 1. Thumbnail with Cropping

```
/img/storage/evidence/9/image.jpg?w=300&h=200&fit=crop
```
This creates a 300×200 pixel cropped thumbnail.

### 2. Optimized WebP Image with Adjusted Quality

```
/img/storage/evidence/9/image.jpg?fm=webp&q=85
```
Converts the image to WebP with 85% quality.

### 3. Resized Grayscale Image

```
/img/storage/evidence/9/image.jpg?w=500&filt=greyscale
```
Resizes to 500px width and applies grayscale filter.

### 4. Focused Image for Retina Display

```
/img/storage/evidence/9/image.jpg?w=600&dpr=2&sharp=15
```
Image for retina displays (1200px actual) with enhanced sharpness.

### 5. Smart Center Cropping

```
/img/storage/evidence/9/image.jpg?w=400&h=400&fit=crop&crop=faces
```
Crops the image to 400×400px, trying to keep faces in the frame.

## WebP Optimization

The system allows automatically converting images to WebP to improve loading times.

### Basic Usage

```
/img/storage/evidence/9/image.jpg?fm=webp
```

### With Additional Adjustments

```
/img/storage/evidence/9/image.jpg?fm=webp&q=80&w=600
```

### From Models with the HasImages Trait

```php
$producto->webpUrl(600); // 600px width, WebP format
$producto->webpUrl(400, 300, 'crop', 90); // 400x300, cropped, quality 90
```

## Image Presets

The system includes predefined presets that facilitate consistent use of images throughout the application.

### Predefined Presets

The following presets are configured by default:

- **thumbnail**: 150x150px, WebP format, center crop
- **medium**: 400px width, WebP format, proportional fit
- **large**: 800px width, WebP format, proportional fit
- **social**: 1200x630px, JPG format, center crop (optimized for sharing on social networks)

### Using Presets

```php
// In models using the HasImages trait
$producto->imagePreset('thumbnail');
$producto->imagePreset('medium');
$producto->imagePreset('large');
$producto->imagePreset('social');
```

### Customizing Presets

You can customize presets in the `config/images.php` file:

```php
'presets' => [
  'thumbnail' => ['dimensions' => '150x150', 'format' => 'webp', 'fit' => 'crop'],
  'medium' => ['dimensions' => '400', 'format' => 'webp', 'fit' => 'max'],
  'large' => ['dimensions' => '800', 'format' => 'webp', 'fit' => 'max'],
  'social' => ['dimensions' => '1200x630', 'format' => 'jpg', 'fit' => 'crop'],
  // Add custom presets here
  'my_preset' => ['dimensions' => '500x300', 'format' => 'webp', 'fit' => 'crop'],
],
```

## Default Images

The system now provides improved management of default images, based on configuration.

### Configuration

Default images are configured in `config/images.php`:

```php
'defaults' => [
  'default' => 'defaults/no-image.jpg',
  'products' => 'defaults/product.jpg',
  'users' => 'defaults/user.jpg',
  'evidence' => 'defaults/evidence.jpg',
  'documents' => 'defaults/document.jpg',
],
```

### How It Works

When an image that doesn't exist is requested, the system:

1. Determines the image category based on the first segment of the path
2. Looks for a default image for that category in the configuration
3. If it doesn't find a specific image for the category, it uses the 'default' image
4. Applies the same processing parameters to the default image

### Adding New Default Images

To add default images:

1. Place the images in `storage/app/public/defaults/`
2. Update the configuration file `config/images.php`
3. Run `php artisan storage:link` if you haven't created the symbolic link yet

## Cache Maintenance

The system automatically caches processed images to improve performance. To manage the cache:

### Register the Cleaning Command

First, you need to register the command in Laravel. To do this, edit the `app/Console/Kernel.php` file and add the command to the `$commands` property:

```php
protected $commands = [
  \App\Console\Commands\CleanImageCache::class,
  // Other commands...
];
```

Starting from Laravel 8, you can register the command automatically by placing it in the `app/Console/Commands` directory.

### Cleaning Command

An Artisan command is provided to clean the image cache:

```bash
# Clean images older than 30 days (default value)
php artisan images:clean-cache

# Clean images older than a specific number of days
php artisan images:clean-cache --days=7
```

### Cleaning Schedule

To automate cleaning, you can add the command to the task scheduler in `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
  // Clean the image cache once a week
  $schedule->command('images:clean-cache')->weekly();

  // Or specify custom days
  // $schedule->command('images:clean-cache --days=15')->weekly();
}
```

Remember to configure the task scheduler to run according to Laravel documentation:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### Cache Configuration

You can configure the cache lifetime in `config/images.php`:

```php
'cache' => [
  'path' => 'cache/img',
  'lifetime' => 30, // days
],
```

## Advanced Usage

### Responsive Images with srcset

```html
<img
  src="{{ $producto->fotoUrl('600') }}"
  srcset="{{ $producto->webpUrl(600) }} 600w,
          {{ $producto->webpUrl(1200) }} 1200w"
  sizes="(max-width: 600px) 100vw, 600px"
  alt="{{ $producto->nombre }}">
```

### Picture with WebP and Fallback

```html
<picture>
  <source
    type="image/webp"
    srcset="{{ $producto->webpUrl(800) }}">
  <img
    src="{{ $producto->fotoUrl(800) }}"
    alt="{{ $producto->nombre }}">
</picture>
```

### Methods in viewhelper

If you prefer a helper to generate URLs:

```php
// In a service provider or custom helper
function imageUrl($path, array $params = []) {
  $url = url('/img/' . ltrim($path, '/'));

  if (!empty($params)) {
    $url .= '?' . http_build_query($params);
  }

  return $url;
}

// Usage
<img src="{{ imageUrl('storage/products/1.jpg', ['w' => 300, 'h' => 200, 'fm' => 'webp']) }}">
```

## Technical Implementation

### ImageController

The `ImageController` handles image processing and HTTP response:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Glide\GlideImage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ImageController extends Controller
{
  public function show(Request $request, $path)
  {
    // If the path starts with "storage/", adjust the search to use the 'public' disk
    $disk = 'local';
    if (strpos($path, 'storage/') === 0) {
      $path = substr($path, strlen('storage/'));
      $disk = 'public';
    }

    // Check if the image exists on the appropriate disk
    if (!Storage::disk($disk)->exists($path)) {
      // Try to get a default image based on the file type
      $defaultImage = $this->getDefaultImageByPath($path);
      if ($defaultImage) {
        return $this->processDefaultImage($defaultImage, $request);
      }

      throw new NotFoundHttpException("Image not found: {$path}");
    }

    // If there are no manipulation parameters, serve the original image
    if (empty($request->all())) {
      return $this->serveGlideImage(Storage::disk($disk)->path($path));
    }

    // Create a unique hash for this combination of image and parameters
    $paramsHash = md5(json_encode($request->all()));
    $cachePath = "cache/img/{$disk}/" . dirname($path) . '/' . $paramsHash . '_' . basename($path);

    // Check if it exists in cache
    if (Storage::exists($cachePath)) {
      return $this->serveGlideImage(Storage::path($cachePath), true);
    }

    // Prepare cache directory
    $cacheDirectory = dirname(Storage::path($cachePath));
    if (!file_exists($cacheDirectory)) {
      mkdir($cacheDirectory, 0755, true);
    }

    try {
      // Get the full path of the image from the appropriate disk
      $sourcePath = Storage::disk($disk)->path($path);

      // Manipulate the image with spatie/laravel-glide
      GlideImage::create($sourcePath)
        ->modify($request->all())
        ->save(Storage::path($cachePath));
    } catch (\Exception $e) {
      Log::error("Error manipulating image: " . $e->getMessage(), [
        'path' => $path,
        'params' => $request->all()
      ]);
      throw $e;
    }

    return $this->serveGlideImage(Storage::path($cachePath), false);
  }

  /**
   * Serves an image to the browser
   */
  protected function serveGlideImage($imagePath, $fromCache = null)
  {
    if (!file_exists($imagePath)) {
      throw new NotFoundHttpException("Image not found in file system");
    }

    $response = new Response(file_get_contents($imagePath), 200);
    $response->headers->set('Content-Type', mime_content_type($imagePath));
    $response->headers->set('Cache-Control', 'public, max-age=31536000');

    if ($fromCache !== null) {
      $response->headers->set('X-Image-Cache', $fromCache ? 'HIT' : 'MISS');
    }

    return $response;
  }

  /**
   * Gets a default image based on the requested path
   *
   * @param string $path Requested image path
   * @return array|null Default image information or null if none
   */
  protected function getDefaultImageByPath($path)
  {
    // Extract information from the path to determine image type
    $pathParts = explode('/', $path);
    $category = count($pathParts) > 0 ? $pathParts[0] : '';

    // Try to get category mappings from configuration
    // With fallback to an empty array if it doesn't exist
    $defaultImages = Config::get('images.defaults', [
      'default' => 'defaults/no-image.jpg'
    ]);

    // First try with the specific category
    if (array_key_exists($category, $defaultImages)) {
      $defaultPath = $defaultImages[$category];
    }
    // Then try with a generic image
    else if (array_key_exists('default', $defaultImages)) {
      $defaultPath = $defaultImages['default'];
    }
    // Finally use a hardcoded fallback as a last resort
    else {
      $defaultPath = 'defaults/no-image.jpg';
    }

    // Look for the image on the public disk first
    if (Storage::disk('public')->exists($defaultPath)) {
      return ['disk' => 'public', 'path' => $defaultPath];
    }

    // If it doesn't exist in public, look on the local disk
    if (Storage::exists($defaultPath)) {
      return ['disk' => 'local', 'path' => $defaultPath];
    }

    return null;
  }

  /**
   * Processes and serves a default image
   *
   * @param array $defaultImage Default image information
   * @param Request $request Current request
   * @return Response HTTP response with the image
   */
  protected function processDefaultImage($defaultImage, $request)
  {
    $disk = $defaultImage['disk'];
    $path = $defaultImage['path'];

    // If there are no manipulation parameters, serve the original image
    if (empty($request->all())) {
      return $this->serveGlideImage(Storage::disk($disk)->path($path));
    }

    // Create a unique hash for this combination of image and parameters
    $paramsHash = md5(json_encode($request->all()));
    $cachePath = "cache/img/defaults/" . $paramsHash . '_' . basename($path);

    // Check if it exists in cache
    if (Storage::exists($cachePath)) {
      return $this->serveGlideImage(Storage::path($cachePath), true);
    }

    // Prepare cache directory
    $cacheDirectory = dirname(Storage::path($cachePath));
    if (!file_exists($cacheDirectory)) {
      mkdir($cacheDirectory, 0755, true);
    }

    try {
      // Get the full path of the image from the appropriate disk
      $sourcePath = Storage::disk($disk)->path($path);

      // Manipulate the image with spatie/laravel-glide
      GlideImage::create($sourcePath)
        ->modify($request->all())
        ->save(Storage::path($cachePath));
    } catch (\Exception $e) {
      Log::error("Error manipulating default image: " . $e->getMessage(), [
        'path' => $path,
        'params' => $request->all()
      ]);
      // In case of error with the default image, we try to serve the original
      return $this->serveGlideImage(Storage::disk($disk)->path($path));
    }

    return $this->serveGlideImage(Storage::path($cachePath), false);
  }
}
```

### HasImages Trait

The `HasImages` trait makes using image processing from models easier:

```php
<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasImages
{
  /**
   * Generates the URL for a processed image associated with this model
   *
   * @param string|null $dimensions Format: '300' or '300x200' (width x height)
   * @param string $format Output format (jpg, png, webp, pjpg)
   * @param string|null $watermark Watermark to apply
   * @param array $extraParams Additional parameters for Glide
   * @return string URL of the processed image
   */
  public function fotoUrl($dimensions = null, $format = 'pjpg', $watermark = null, $extraParams = [])
  {
    // Get the image path from the model property
    $imagePath = $this->getFotoPath();

    // If there's no image, return a default image
    if (empty($imagePath)) {
      return $this->getDefaultImageUrl();
    }

    // If there are no dimensions, return the original URL
    if (empty($dimensions)) {
      return $this->getOriginalImageUrl($imagePath);
    }

    // Prepare the Glide parameters
    $params = $this->prepareImageParams($dimensions, $format, $watermark, $extraParams);

    // Generate and return the URL
    return $this->generateImageUrl($imagePath, $params);
  }

  /**
   * Get the image path for this model (customizable in each model)
   *
   * @return string|null
   */
  protected function getFotoPath()
  {
    // Default implementation - override in specific models if necessary
    if (isset($this->foto)) {
      return $this->foto;
    } elseif (isset($this->imagen)) {
      return $this->imagen;
    } elseif (isset($this->image)) {
      return $this->image;
    }

    return null;
  }

  /**
   * Get the default URL for when there's no image
   *
   * @return string
   */
  protected function getDefaultImageUrl()
  {
    // Override in specific models if necessary
    return asset('img/no-image.jpg');
  }

  /**
   * Get the original URL of the image without processing
   *
   * @param string $path
   * @return string
   */
  protected function getOriginalImageUrl($path)
  {
    // If the path is already a complete URL
    if (Str::startsWith($path, ['http://', 'https://'])) {
      return $path;
    }

    // If the path starts with 'storage/'
    if (Str::startsWith($path, 'storage/')) {
      return asset($path);
    }

    // If the path doesn't specify storage, we assume it's in storage
    return asset('storage/' . $path);
  }

  /**
   * Prepare the parameters for image processing
   *
   * @param string $dimensions
   * @param string $format
   * @param string|null $watermark
   * @param array $extraParams
   * @return array
   */
  protected function prepareImageParams($dimensions, $format, $watermark, array $extraParams)
  {
    $params = $extraParams;

    // Interpret dimensions (300 or 300x200)
    if (strpos($dimensions, 'x') !== false) {
      list($width, $height) = explode('x', $dimensions);
      $params['w'] = (int)$width;
      $params['h'] = (int)$height;
      $params['fit'] = $params['fit'] ?? 'crop';
    } else {
      $params['w'] = (int)$dimensions;
      $params['fit'] = $params['fit'] ?? 'max';
    }

    // Format
    if ($format) {
      $params['fm'] = $format;

      // For JPEG and PJPG, set default quality if not specified
      if (in_array($format, ['jpg', 'pjpg']) && !isset($params['q'])) {
        $params['q'] = 85;
      }
    }

    // Watermark (if enabled)
    if ($watermark) {
      $params['mark'] = $watermark;
      if (!isset($params['markw'])) {
        $params['markw'] = 50; // Watermark width as % of the image
      }
      if (!isset($params['markalpha'])) {
        $params['markalpha'] = 60; // Transparency (0-100)
      }
      if (!isset($params['markpos'])) {
        $params['markpos'] = 'bottom-right'; // Position
      }
    }

    return $params;
  }

  /**
   * Generate the final URL for the processed image
   *
   * @param string $path
   * @param array $params
   * @return string
   */
  protected function generateImageUrl($path, array $params)
  {
    // Make sure the path doesn't start with /
    $path = ltrim($path, '/');

    // If it doesn't start with storage/, add it
    if (!Str::startsWith($path, 'storage/')) {
      $path = 'storage/' . $path;
    }

    // Build the URL with the parameters
    $url = url('/img/' . $path);

    if (!empty($params)) {
      $url .= '?' . http_build_query($params);
    }

    return $url;
  }

  /**
   * Generates a URL for a responsive WebP image
   *
   * @param int $width Desired width
   * @param int|null $height Desired height (optional)
   * @param string $fit Fit method (crop, contain, max, fill)
   * @param int $quality Image quality (0-100)
   * @return string
   */
  public function webpUrl($width, $height = null, $fit = 'max', $quality = 85)
  {
    $dimensions = $height ? "{$width}x{$height}" : (string)$width;
    return $this->fotoUrl($dimensions, 'webp', null, [
      'fit' => $fit,
      'q' => $quality
    ]);
  }

  /**
   * Applies predefined manipulations to images
   *
   * @param string $preset Preset name (thumbnail, medium, large, etc)
   * @return string URL of the processed image
   */
  public function imagePreset($preset = 'thumbnail')
  {
    $presets = config('images.presets', [
      'thumbnail' => ['dimensions' => '150x150', 'format' => 'webp', 'fit' => 'crop'],
      'medium' => ['dimensions' => '400', 'format' => 'webp', 'fit' => 'max'],
      'large' => ['dimensions' => '800', 'format' => 'webp', 'fit' => 'max'],
      'social' => ['dimensions' => '1200x630', 'format' => 'jpg', 'fit' => 'crop'],
    ]);

    if (!isset($presets[$preset])) {
      $preset = 'thumbnail';
    }

    $config = $presets[$preset];

    return $this->fotoUrl(
      $config['dimensions'],
      $config['format'] ?? 'webp',
      null,
      ['fit' => $config['fit'] ?? 'max']
    );
  }
}
```

### Cache Cleaning Command

```php
<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

class CleanImageCache extends Command
{
  protected $signature = 'images:clean-cache {--days=0}';
  protected $description = 'Cleans cached images older than the specified number of days';

  public function handle()
  {
    // Use the parameter value or the configuration value or 30 days as default
    $days = $this->option('days') ?: Config::get('images.cache.lifetime', 30);

    $cutoffDate = Carbon::now()->subDays($days);

    $this->info("Cleaning cached images older than {$cutoffDate->format('Y-m-d')}");

    $cacheDirectory = Config::get('images.cache.path', 'cache/img');
    $files = Storage::allFiles($cacheDirectory);
    $count = 0;

    foreach ($files as $file) {
      $lastModified = Carbon::createFromTimestamp(Storage::lastModified($file));
      if ($lastModified->lt($cutoffDate)) {
        Storage::delete($file);
        $count++;
      }
    }

    $this->info("{$count} cache files were deleted.");
  }
}
```

---

This manual covers all aspects of the image processing system implemented in the application, including the new features of configurable presets, category-based default images, and cache maintenance. For more details on Glide parameters, see the official [Glide](https://glide.thephpleague.com/) documentation.
