# Laravel Glide Enhanced

A Laravel package for dynamic image processing based on [Spatie/Laravel-Glide](https://github.com/spatie/laravel-glide).

## ⚠️ Important Update (v2.x)

The default route prefix has changed from `/img/` to `/glide/` to avoid conflicts with other packages, especially `laravel-dropzone-enhanced`.

**Before:** `https://your-site.com/img/path/image.jpg`  
**Now:** `https://your-site.com/glide/path/image.jpg`

See the [UPGRADE.md](UPGRADE.md) file for migration instructions.

## Features

- Dynamic image processing (resizing, cropping, format conversion)
- Image optimization for web use
- Automatic caching for improved performance
- Configurable default images by category
- Predefined presets for consistent image usage
- `HasImages` trait for Eloquent models
- `ImageProcessor` facade for easy access
- Configurable routes to avoid conflicts with other packages
- **Full compatibility with Laravel Dropzone Enhanced**

## Current Limitations

- The package currently works only with local files (using the 'local' and 'public' disks)
- External storage services like AWS S3 are not fully supported for image processing
- URLs from external services are recognized but cannot be dynamically processed

## Installation

You can install the package via composer:

```bash
composer require maccesar/laravel-glide-enhanced
```

The package will automatically register its service provider and facade.

Then publish the configuration file:

```bash
php artisan vendor:publish --tag=images-config
```

## Requirements

- PHP 8.0 or higher
- Laravel 8.x, 9.x, 10.x, 11.x, or 12.x
- [Spatie's Laravel Glide package](https://github.com/spatie/laravel-glide)

## Configuration

The `config/images.php` file contains several configuration options:

```php
return [
    // Image cache configuration
    'cache' => [
        'lifetime' => 30, // days
        'path' => 'cache/glide',
    ],

    // Default processing settings
    'defaults' => [
        'fit' => 'max',     // Default fit mode (max, crop, fill, stretch)
        'quality' => 85,    // Default quality (0-100)
        'format' => 'webp', // Default output format (webp, jpg, png, etc.)
    ],

    // Routes configuration
    'routes' => [
        'enabled' => true,       // Enable/disable package routes
        'prefix' => 'glide',     // URL prefix for the image routes
        'middleware' => ['web'],  // Middleware to apply to the routes
    ],

    // Disk configuration. Change to 'local' if your images are in /storage/app
    'disk' => 'public',

    // Default fallback images by category
    'fallback_images' => [
        'default' => 'defaults/no-image.jpg',
        'documents' => 'defaults/document.jpg',
        'evidence' => 'defaults/evidence.jpg',
        'products' => 'defaults/product.jpg',
        'users' => 'defaults/user.jpg',
    ],

    // Predefined presets for image conversion
    'presets' => [
        'large' => ['dimensions' => '800', 'format' => 'webp', 'fit' => 'max'],
        'medium' => ['dimensions' => '400', 'format' => 'webp', 'fit' => 'max'],
        'social' => ['dimensions' => '1200x630', 'format' => 'jpg', 'fit' => 'crop'],
        'thumbnail' => ['dimensions' => '150x150', 'format' => 'webp', 'fit' => 'crop'],
    ],
];
```

### Disk Configuration

The `disk` configuration allows you to specify where your images are stored. By default, the package uses the `public` disk, which corresponds to the `/storage/app/public` directory. If your images are stored in a different location, such as the root of `/storage/app`, you can change the disk to `local` or any other disk defined in your Laravel filesystem configuration.

```php
'disk' => 'public', // Change to 'local' if your images are in /storage/app
```

For example, if your images are stored in `/storage/app`, update the configuration as follows:

```php
'disk' => 'local',
```

Make sure the disk you specify is properly configured in your `config/filesystems.php` file.

### Route Configuration

The routes section allows you to customize how the package registers its routes:

```php
'routes' => [
    'enabled' => true,        // Set to false to disable all package routes
    'prefix' => 'glide',      // Change the URL prefix (e.g., 'images', 'media', etc.)
    'middleware' => ['web'],  // Add or modify middleware applied to the routes
],
```

If you need to avoid conflicts with other packages, you can change the route prefix or disable the routes entirely and implement your own.

## Basic Usage

### Facades Available

The package provides two facades for your convenience:

```php
// Option 1: Full facade name
use MacCesar\LaravelGlideEnhanced\Facades\ImageProcessor;

// Option 2: Short alias (recommended for cleaner code)
use MacCesar\LaravelGlideEnhanced\Facades\Img;
```

Both facades provide identical functionality. We recommend using `Img` for cleaner, more readable code.

### Image Processing URLs

```php
use MacCesar\LaravelGlideEnhanced\Facades\Img;

// Basic URL with parameters
Img::url('path/to/image.jpg', ['w' => 300, 'h' => 200, 'fit' => 'crop']);

// WebP optimized URL
Img::webpUrl('path/to/image.jpg', ['w' => 300, 'h' => 200, 'fit' => 'crop', 'q' => 90]);

// Use a predefined preset
Img::preset('path/to/image.jpg', 'thumbnail');
```

### HasImages Trait for Eloquent Models

```php
use MacCesar\LaravelGlideEnhanced\Traits\HasImages;

class Product extends Model
{
    use HasImages;

    // ...
}

// Usage in code
$product->getImageWebpUrl('main');
$product->getImageUrl('main', '300x200', 'webp');
$product->getImagePreset('main', 'thumbnail');
```

## HasImages Trait Reference

The `HasImages` trait provides comprehensive image processing methods for Eloquent models. Add this trait to any model that needs dynamic image processing capabilities.

### Setup

```php
use MacCesar\LaravelGlideEnhanced\Traits\HasImages;

class Product extends Model
{
    use HasImages;
    
    // Your model code...
}
```

### Available Methods

| Method                                                               | Parameters                                                                                                                                | Return Type | Description                                |
| -------------------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------- | ----------- | ------------------------------------------ |
| `getImageUrl($type, $dimensions, $format, $watermark, $extraParams)` | `string $type = 'default'`, `string $dimensions = null`, `string $format = 'pjpg'`, `string $watermark = null`, `array $extraParams = []` | `string`    | Generate image URL with custom parameters  |
| `getImageWebpUrl($type, $params)`                                    | `string $type = 'default'`, `array $params = []`                                                                                          | `string`    | Generate WebP optimized image URL          |
| `getImagePreset($type, $preset)`                                     | `string $type = 'default'`, `string $preset = 'thumbnail'`                                                                                | `string`    | Generate image URL using predefined preset |

### Method Details

#### `getImageUrl($type = 'default', $dimensions = null, $format = 'pjpg', $watermark = null, $extraParams = [])`

Generate a custom image URL with specific parameters.

**Parameters:**
- `$type` (string): Image type identifier (default, main, thumbnail, etc.)
- `$dimensions` (string|null): Format: '300' or '300x200' (width x height)
- `$format` (string): Output format (jpg, png, webp, pjpg)
- `$watermark` (string|null): Watermark to apply
- `$extraParams` (array): Additional parameters for Glide

**Example:**
```php
$product = Product::find(1);

// Custom resize with crop
$imageUrl = $product->getImageUrl('main', '400x300', 'webp', null, [
    'fit' => 'crop',
    'q' => 90
]);

// With watermark and effects
$imageUrl = $product->getImageUrl('main', '600', 'jpg', 'watermarks/logo.png', [
    'blur' => 5,
    'bright' => 10,
    'markpos' => 'bottom-right'
]);

// Just width, no processing
$imageUrl = $product->getImageUrl('main'); // Returns original URL
```

#### `getImageWebpUrl($type = 'default', array $params = [])`

Generate a WebP optimized image URL using the ImageProcessor facade.

**Parameters:**
- `$type` (string): Image type identifier (default, main, thumbnail, etc.)
- `$params` (array): Glide parameters for image processing

**Example:**
```php
$product = Product::find(1);

// Simple WebP with default settings
$webpUrl = $product->getImageWebpUrl('main');

// WebP with custom parameters
$webpUrl = $product->getImageWebpUrl('main', [
    'w' => 600,
    'fit' => 'crop',
    'q' => 95
]);
```

#### `getImagePreset($type = 'default', $preset = 'thumbnail')`

Generate image URL using a predefined preset configuration.

**Parameters:**
- `$type` (string): Image type identifier (default, main, thumbnail, etc.)
- `$preset` (string): Preset name (thumbnail, medium, large, social)

**Example:**
```php
$product = Product::find(1);

$thumbnail = $product->getImagePreset('main', 'thumbnail'); // 150x150 WebP cropped
$medium = $product->getImagePreset('main', 'medium');       // 400px WebP
$large = $product->getImagePreset('main', 'large');         // 800px WebP
$social = $product->getImagePreset('main', 'social');       // 1200x630 JPG for social sharing
```

### Image Path Resolution

The trait automatically resolves image paths using these strategies:

1. **Custom methods**: `get{Type}ImagePath()` method in your model
2. **Model properties**: Direct property access (`$this->{$type}`)
3. **Backward compatibility**: For 'default' type, checks `image`, `imagen`, or `foto` properties

**Example:**
```php
class Product extends Model
{
    use HasImages;
    
    // Method 1: Custom method for specific type
    public function getMainImagePath()
    {
        return $this->main_photo;
    }
    
    // Method 2: Direct property access
    // $product->gallery_1, $product->featured, etc.
}

// Usage
$product->getImageUrl('main');      // Uses getMainImagePath()
$product->getImageUrl('gallery_1'); // Uses $product->gallery_1 property
$product->getImageUrl('default');   // Uses $product->image property
```

## Image Presets

Laravel Glide Enhanced includes predefined presets for common image sizes and formats. These presets ensure consistent image processing across your application.

### Available Presets

| Preset      | Dimensions  | Format | Fit Mode | Quality | Best Used For                                     |
| ----------- | ----------- | ------ | -------- | ------- | ------------------------------------------------- |
| `thumbnail` | 150x150     | WebP   | crop     | 85      | Avatar images, small previews, gallery thumbnails |
| `medium`    | 400px width | WebP   | max      | 85      | Card images, product listings, blog post previews |
| `large`     | 800px width | WebP   | max      | 85      | Featured images, hero sections, detailed views    |
| `social`    | 1200x630    | JPG    | crop     | 85      | Social media sharing (Open Graph, Twitter Cards)  |

### Preset Configuration

The presets are defined in your `config/images.php` file:

```php
'presets' => [
    'thumbnail' => [
        'dimensions' => '150x150', 
        'format' => 'webp', 
        'fit' => 'crop'
    ],
    'medium' => [
        'dimensions' => '400', 
        'format' => 'webp', 
        'fit' => 'max'
    ],
    'large' => [
        'dimensions' => '800', 
        'format' => 'webp', 
        'fit' => 'max'
    ],
    'social' => [
        'dimensions' => '1200x630', 
        'format' => 'jpg', 
        'fit' => 'crop'
    ],
],
```

### Custom Presets

You can create your own presets by adding them to the configuration:

```php
'presets' => [
    // Default presets...
    
    // Custom presets
    'product_card' => [
        'dimensions' => '300x300',
        'format' => 'webp',
        'fit' => 'crop',
        'quality' => 90
    ],
    'banner' => [
        'dimensions' => '1920x400',
        'format' => 'jpg',
        'fit' => 'crop',
        'quality' => 95
    ],
    'mobile_hero' => [
        'dimensions' => '768x400',
        'format' => 'webp',
        'fit' => 'crop',
        'quality' => 85
    ],
],
```

### Using Custom Presets

```php
use MacCesar\LaravelGlideEnhanced\Facades\Img;

// Using Img facade
$cardImage = Img::preset('products/image.jpg', 'product_card');
$bannerImage = Img::preset('banners/hero.jpg', 'banner');

// Using HasImages trait
$product = Product::find(1);
$cardImage = $product->getImagePreset('main', 'product_card');
$bannerImage = $product->getImagePreset('main', 'banner');
```

### Preset Best Practices

1. **thumbnail**: Perfect for user avatars, small product images in lists
2. **medium**: Ideal for product cards, blog post featured images
3. **large**: Best for hero images, detailed product views
4. **social**: Optimized for social media sharing (Facebook, Twitter, LinkedIn)

**Performance Tips:**
- Use WebP format for modern browsers (better compression)
- Use JPG for social media sharing (better compatibility)
- Crop fit mode for consistent dimensions, max fit mode to preserve aspect ratio

### Responsive Images with srcset

The package provides a convenient way to generate srcset attributes for responsive images with different pixel densities:

```php
use MacCesar\LaravelGlideEnhanced\Facades\Img;

// Generate srcset for 1x, 2x, and 3x pixel densities
Img::srcset('path/to/image.jpg', ['w' => 300, 'fm' => 'webp']);
// Output: "/glide/storage/path/to/image.jpg?w=300&fm=webp 1x, /glide/storage/path/to/image.jpg?w=600&fm=webp 2x, /glide/storage/path/to/image.jpg?w=900&fm=webp 3x"

// Control the maximum density factor (e.g., up to 2x)
Img::srcset('path/to/image.jpg', ['w' => 300, 'h' => 200, 'fm' => 'webp'], 2);
// Output: "/glide/storage/path/to/image.jpg?w=300&h=200&fm=webp 1x, /glide/storage/path/to/image.jpg?w=600&h=400&fm=webp 2x"
```

## Quick Reference

### Common Use Cases

```php
// Blog post featured image
$blog->getImagePreset('featured', 'large');

// User avatar
$user->getImagePreset('avatar', 'thumbnail');

// Product gallery
$product->getImageWebpUrl('gallery_1', ['w' => 600]);

// Social sharing image
$post->getImagePreset('cover', 'social');

// Responsive hero image
Img::srcset('hero/main.jpg', ['w' => 800, 'fm' => 'webp']);
```

### Most Used Glide Parameters

| Parameter | Values                   | Description                     | Example                       |
| --------- | ------------------------ | ------------------------------- | ----------------------------- |
| `w`       | integer                  | Width in pixels                 | `'w' => 400`                  |
| `h`       | integer                  | Height in pixels                | `'h' => 300`                  |
| `fit`     | crop, max, fill, stretch | How image should fit dimensions | `'fit' => 'crop'`             |
| `fm`      | webp, jpg, png, gif      | Output format                   | `'fm' => 'webp'`              |
| `q`       | 0-100                    | Quality (lower = smaller file)  | `'q' => 90`                   |
| `blur`    | 0-100                    | Blur effect                     | `'blur' => 5`                 |
| `bright`  | -100 to 100              | Brightness adjustment           | `'bright' => 10`              |
| `mark`    | path                     | Watermark image path            | `'mark' => 'logo.png'`        |
| `markpos` | position                 | Watermark position              | `'markpos' => 'bottom-right'` |

### Fit Mode Reference

- **crop**: Crops image to exact dimensions (may cut off parts)
- **max**: Resizes to fit within dimensions (preserves aspect ratio)
- **fill**: Fills dimensions exactly (may stretch image)
- **stretch**: Stretches to exact dimensions (ignores aspect ratio)

### Applying Watermarks

You can apply watermarks to your images using the following parameters:

```php
use MacCesar\LaravelGlideEnhanced\Facades\Img;

// Apply watermark
Img::url('path/to/image.jpg', ['w' => 600, 'mark' => 'watermarks/logo.png', 'markw' => 200, 'markpos' => 'bottom-right', 'markalpha' => 60]);

// Using HasImages trait with watermark
$product->getImageUrl('main', '600', 'jpg', 'watermarks/logo.png', [
    'markw' => 200,
    'markpos' => 'bottom-right',
    'markalpha' => 60
]);
```

> **Important note**: The watermark image must exist at the specified path within `storage/app/public/`. For example, if you use `'mark' => 'watermarks/logo.png'`, the file should be located at `storage/app/public/watermarks/logo.png`.

## Usage in Blade Templates

### Basic Image Display

```blade
{{-- Product with optimized image --}}
@php
    $product = App\Models\Product::find(1);
@endphp

<!-- Simple optimized image -->
<img src="{{ $product->getImageWebpUrl('main', ['w' => 600]) }}" 
     alt="{{ $product->name }}"
     class="w-full h-64 object-cover rounded-lg">

<!-- Using preset -->
<img src="{{ $product->getImagePreset('main', 'large') }}" 
     alt="{{ $product->name }}"
     class="w-full h-auto">

<!-- Custom dimensions and format -->
<img src="{{ $product->getImageUrl('main', '400x300', 'webp') }}" 
     alt="{{ $product->name }}"
     class="w-full h-64 object-cover">
```

### Responsive Images

```blade
<!-- Responsive image with srcset -->
<img src="{{ $product->getImageWebpUrl('main', ['w' => 400]) }}"
     srcset="{{ \MacCesar\LaravelGlideEnhanced\Facades\Img::srcset($product->main, ['w' => 400, 'fm' => 'webp']) }}"
     sizes="(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 400px"
     alt="{{ $product->name }}"
     class="w-full h-auto">

<!-- Different sizes for different breakpoints -->
<picture>
    <source media="(max-width: 768px)" 
            srcset="{{ $product->getImageWebpUrl('main', ['w' => 400]) }}">
    <source media="(max-width: 1200px)" 
            srcset="{{ $product->getImageWebpUrl('main', ['w' => 600]) }}">
    <img src="{{ $product->getImageWebpUrl('main', ['w' => 800]) }}" 
         alt="{{ $product->name }}"
         class="w-full h-auto">
</picture>
```

### Product Gallery

```blade
<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    @foreach(['gallery_1', 'gallery_2', 'gallery_3', 'gallery_4'] as $photo)
        @if($product->{$photo})
            <div class="aspect-square">
                <!-- Thumbnail with lightbox -->
                <a href="{{ $product->getImagePreset($photo, 'large') }}" 
                   data-lightbox="gallery">
                    <img src="{{ $product->getImagePreset($photo, 'thumbnail') }}" 
                         alt="{{ $product->name }} - {{ $photo }}"
                         class="w-full h-full object-cover rounded-lg hover:opacity-90 transition">
                </a>
            </div>
        @endif
    @endforeach
</div>
```

### Blog Post with Social Meta

```blade
@php
    $blog = App\Models\Blog::find(1);
@endphp

<!-- Social media meta tags -->
<meta property="og:image" content="{{ $blog->getImagePreset('featured', 'social') }}">
<meta name="twitter:image" content="{{ $blog->getImagePreset('featured', 'social') }}">

<!-- Blog post header -->
<article>
    <header>
        <img src="{{ $blog->getImagePreset('featured', 'large') }}" 
             alt="{{ $blog->title }}"
             class="w-full h-64 md:h-96 object-cover rounded-xl mb-6">
        
        <h1 class="text-3xl font-bold mb-4">{{ $blog->title }}</h1>
    </header>
    
    <div class="prose max-w-none">
        {!! $blog->content !!}
    </div>
</article>
```

### E-commerce Product Card

```blade
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <!-- Product image with hover effect -->
    <div class="aspect-square bg-gray-100">
        <img src="{{ $product->getImagePreset('main', 'medium') }}" 
             alt="{{ $product->name }}"
             class="w-full h-full object-cover transition duration-300 hover:scale-105">
    </div>
    
    <!-- Product info -->
    <div class="p-4">
        <h3 class="font-semibold text-lg mb-2">{{ $product->name }}</h3>
        <p class="text-gray-600 text-sm mb-3">{{ $product->description }}</p>
        <div class="flex justify-between items-center">
            <span class="text-xl font-bold text-green-600">${{ $product->price }}</span>
            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Add to Cart
            </button>
        </div>
    </div>
</div>
```

## Cache Cleaning

To clean the image cache, use the command:

```bash
php artisan images:clean-cache
```

To clean only images older than a specific number of days:

```bash
php artisan images:clean-cache --days=7
```

## Components Registered

This package registers the following components in Laravel:

- **Routes**: A route with the prefix `/glide/` (configurable via the `routes.prefix` config)
- **Services**: A singleton `image-processor` in the container
- **Facade**: `ImageProcessor` for easy access to the service
- **Command**: `images:clean-cache` for cache maintenance

## Troubleshooting

### Common Issues

#### Images Not Processing

**Problem**: Images return original size instead of processed versions.

**Solutions:**
1. Check that the `images.php` config file is published:
   ```bash
   php artisan vendor:publish --tag=images-config
   ```

2. Verify the disk configuration in `config/images.php`:
   ```php
   'disk' => 'public', // or 'local' depending on your setup
   ```

3. Ensure images exist in the specified storage disk:
   ```bash
   ls -la storage/app/public/
   ```

4. Clear image cache:
   ```bash
   php artisan images:clean-cache
   ```

#### Route Not Found Errors

**Problem**: URLs like `/glide/storage/image.jpg` return 404.

**Solutions:**
1. Check that routes are enabled in config:
   ```php
   'routes' => [
       'enabled' => true,
       'prefix' => 'glide',
   ],
   ```

2. Clear route cache:
   ```bash
   php artisan route:clear
   php artisan config:clear
   ```

3. Verify routes are registered:
   ```bash
   php artisan route:list | grep glide
   ```

#### WebP Images Not Working

**Problem**: WebP images not displaying in some browsers.

**Solutions:**
1. Provide fallback using `<picture>` element:
   ```blade
   <picture>
       <source srcset="{{ $model->getImageWebpUrl('main', ['w' => 800]) }}" type="image/webp">
       <img src="{{ $model->getImageUrl('main', '800', 'jpg') }}" alt="...">
   </picture>
   ```

2. Check server WebP support by testing a direct URL

#### Performance Issues

**Problem**: Slow image loading or processing.

**Solutions:**
1. Implement image preloading for critical images:
   ```blade
   <link rel="preload" as="image" href="{{ $hero->getImagePreset('main', 'large') }}">
   ```

2. Use appropriate image formats:
   - WebP for modern browsers (smaller files)
   - JPG for photos with many colors
   - PNG for images with transparency

3. Optimize cache settings in `config/images.php`:
   ```php
   'cache' => [
       'lifetime' => 90, // Increase cache lifetime
       'path' => 'cache/glide',
   ],
   ```

4. Use CDN for static assets in production

### FAQ

**Q: Can I use this with external storage like S3?**
A: Currently, the package works only with local storage disks (`local` and `public`). External storage support is planned for future versions.

**Q: How do I create custom presets?**
A: Add them to your `config/images.php` file under the `presets` array. See the "Custom Presets" section above.

**Q: Can I change the route prefix?**
A: Yes, modify the `routes.prefix` value in your config file. Remember to clear caches after changes.

**Q: How do I disable the package routes?**
A: Set `routes.enabled` to `false` in your config file if you want to implement custom routing.

**Q: What's the difference between `getImageUrl()` and `getImageWebpUrl()`?**
A: `getImageWebpUrl()` uses the ImageProcessor facade and accepts an array of parameters. `getImageUrl()` gives you full control over individual parameters including dimensions, format, and watermarks.

## License

MIT

## Credits

Developed by [César Estrada (macCesar)](https://github.com/macCesar)
