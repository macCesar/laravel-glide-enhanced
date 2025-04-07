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
