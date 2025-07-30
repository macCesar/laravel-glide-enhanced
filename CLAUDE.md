# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Laravel Glide Enhanced is a Laravel package for dynamic image processing, built as an enhancement layer over Spatie/Laravel-Glide. It provides URL-based image manipulation with caching, presets, and model integration.

## Key Commands

### Package Installation & Setup
```bash
composer require maccesar/laravel-glide-enhanced
php artisan vendor:publish --tag=images-config
```

### Cache Management
```bash
# Clean image cache
php artisan images:clean-cache

# Clean cache older than specific days
php artisan images:clean-cache --days=7
```

### Testing Commands
```bash
# Run tests using Orchestra Testbench
vendor/bin/phpunit

# Route debugging
php artisan route:list | grep glide
```

### Configuration Verification
```bash
php artisan config:show images.routes.prefix
php artisan config:clear
php artisan route:clear
```

## Architecture Overview

### Core Components

1. **ImageProcessor** (`src/ImageProcessor.php`): Main service class that generates image URLs with processing parameters
   - `url()`: Basic URL generation with parameters
   - `webpUrl()`: WebP-optimized URLs with defaults
   - `preset()`: Apply predefined image configurations
   - `srcset()`: Generate responsive image srcset attributes

2. **ImageController** (`src/Http/Controllers/ImageController.php`): Handles HTTP requests for image processing
   - Serves images from configured disk (public/local)
   - Implements caching with unique parameter hashes
   - Handles fallback images and watermarks
   - Concurrency-safe cache directory creation

3. **ImageProcessorServiceProvider** (`src/ImageProcessorServiceProvider.php`): Laravel service provider
   - Registers singleton service and facade
   - Publishes configuration and routes
   - Conditionally loads routes based on config

4. **HasImages Trait** (`src/Traits/HasImages.php`): Eloquent model integration
   - `getImageUrl()`: Flexible image URL generation
   - `getImageWebpUrl()`: WebP URL generation
   - `getImagePreset()`: Apply presets to model images
   - Dynamic method resolution for image types

### Configuration System

- **Route Configuration**: Customizable prefix (default: `/glide/`) and middleware
- **Disk Configuration**: Supports `public` and `local` storage disks
- **Cache Settings**: Configurable lifetime and path
- **Presets**: Predefined image sizes and formats (thumbnail, medium, large, social)
- **Fallback Images**: Category-specific default images

### URL Structure

The package generates URLs in the format:
```
/{prefix}/{image-path}?{processing-parameters}
```

Example: `/glide/storage/products/image.jpg?w=300&h=200&fit=crop&fm=webp`

### Image Processing Flow

1. Request received by `ImageController::show()`
2. Check if image exists on configured disk
3. If no processing parameters, serve original
4. Generate cache path using parameter hash
5. Check cache, serve if exists
6. Process image using Spatie/Glide
7. Save to cache and serve

### Dependencies

- **Spatie/Laravel-Glide**: Core image processing functionality
- **Laravel Framework**: 8.x through 12.x support
- **PHP**: 8.0+ required

### Important Notes

- Package works only with local files (local/public disks)
- External storage (S3, etc.) not fully supported for processing
- Route prefix changed from `/img/` to `/glide/` in v2.x to avoid conflicts
- Watermarks must exist in storage/app/public/ directory
- Cache is stored in storage/app/cache/glide/