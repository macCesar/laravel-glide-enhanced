# Laravel Glide Enhanced

A Laravel package for dynamic image processing based on [Spatie/Laravel-Glide](https://github.com/spatie/laravel-glide).

## Features

- Dynamic image processing (resizing, cropping, format conversion)
- Image optimization for web use
- Automatic caching for improved performance
- Configurable default images by category
- Predefined presets for consistent image usage
- `HasImages` trait for Eloquent models
- `ImageProcessor` facade for easy access

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
    // Default images by category
    'defaults' => [
        'default' => 'defaults/no-image.jpg',
        'user' => 'defaults/user.jpg',
        'product' => 'defaults/product.jpg',
    ],

    // Predefined presets for image conversion
    'presets' => [
        'thumbnail' => ['dimensions' => '150x150', 'format' => 'webp', 'fit' => 'crop'],
        'medium' => ['dimensions' => '400', 'format' => 'webp', 'fit' => 'max'],
        'large' => ['dimensions' => '800', 'format' => 'webp', 'fit' => 'max'],
        'social' => ['dimensions' => '1200x630', 'format' => 'jpg', 'fit' => 'crop'],
    ],

    // Image cache configuration
    'cache_days' => 30, // Days to keep images in cache
];
```

## Basic Usage

### Image Processing URLs

```php
use MacCesar\LaravelGlideEnhanced\Facades\ImageProcessor;

// Basic URL with parameters
ImageProcessor::url('path/to/image.jpg', ['w' => 300, 'h' => 200, 'fit' => 'crop']);

// WebP optimized URL
ImageProcessor::webpUrl('path/to/image.jpg', 300, 200, 'crop', 90);

// Use a predefined preset
ImageProcessor::preset('path/to/image.jpg', 'thumbnail');
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
$product->getImageUrl('main', ['w' => 300]);
$product->getImageWebpUrl('main', 300);
$product->getImagePreset('main', 'thumbnail');
```

### Responsive Images with srcset

The package provides a convenient way to generate srcset attributes for responsive images with different pixel densities:

```php
use MacCesar\LaravelGlideEnhanced\Facades\ImageProcessor;

// Generate srcset for 1x, 2x, and 3x pixel densities
ImageProcessor::srcset('path/to/image.jpg', ['w' => 300, 'fm' => 'webp']);
// Output: "/img/storage/path/to/image.jpg?w=300&fm=webp 1x, /img/storage/path/to/image.jpg?w=600&fm=webp 2x, /img/storage/path/to/image.jpg?w=900&fm=webp 3x"

// Control the maximum density factor (e.g., up to 2x)
ImageProcessor::srcset('path/to/image.jpg', ['w' => 300, 'h' => 200, 'fm' => 'webp'], 2);
// Output: "/img/storage/path/to/image.jpg?w=300&h=200&fm=webp 1x, /img/storage/path/to/image.jpg?w=600&h=400&fm=webp 2x"
```

### Applying Watermarks

You can apply watermarks to your images using the following parameters:

```php
use MacCesar\LaravelGlideEnhanced\Facades\ImageProcessor;

// Apply watermark
ImageProcessor::url('path/to/image.jpg', ['w' => 600, 'mark' => 'watermarks/logo.png', 'markw' => 200, 'markpos' => 'bottom-right', 'markalpha' => 60]);
```

> **Important note**: The watermark image must exist at the specified path within `storage/app/public/`. For example, if you use `'mark' => 'watermarks/logo.png'`, the file should be located at `storage/app/public/watermarks/logo.png`.

## Cache Cleaning

To clean the image cache, use the command:

```bash
php artisan images:clean-cache
```

To clean only images older than a specific number of days:

```bash
php artisan images:clean-cache --days=7
```

## License

MIT

## Credits

Developed by [César Estrada (macCesar)](https://github.com/macCesar)
