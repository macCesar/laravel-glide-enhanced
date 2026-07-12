<?php

return [
  /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for processed image cache.
    |
    */
  'cache' => [
    'disk' => 'local',
    'lifetime' => 30, // days
    'path' => 'cache/glide',
    'headers' => [
      'max_age' => 86400,
      'stale_while_revalidate' => 3600,
    ],
  ],

  'source_root' => 'images',
  'watermark_root' => 'watermarks',
  'allowed_mime_types' => ['image/jpeg', 'image/png', 'image/webp'],
  'limits' => [
    'max_width' => 4096,
    'max_height' => 4096,
    'max_megapixels' => 16,
    'max_dpr' => 4,
  ],
  'allowed_parameters' => [
    'w', 'h', 'fit', 'dpr', 'rect', 'or', 'bg', 'border', 'sharp', 'blur',
    'gam', 'bright', 'con', 'sat', 'filt', 'mark', 'markw', 'markh',
    'markfit', 'markx', 'marky', 'markpad', 'markpos', 'markalpha', 'fm', 'q',
  ],

  /*
    |--------------------------------------------------------------------------
    | Default Processing Settings
    |--------------------------------------------------------------------------
    |
    | Default settings for image processing.
    |
    */
  'defaults' => [
    'fit' => 'max',     // Default fit mode (max, crop, fill, stretch)
    'quality' => 85,    // Default quality (0-100)
    'format' => 'webp', // Default output format (webp, jpg, png, etc.)
  ],

  /*
    |--------------------------------------------------------------------------
    | Default Images by Category
    |--------------------------------------------------------------------------
    |
    | Configuration of default images by category.
    | These are used when a requested image is not found.
    |
    */
  'fallback_images' => [
    'default' => 'defaults/no-image.jpg',
    'documents' => 'defaults/document.jpg',
    'evidence' => 'defaults/evidence.jpg',
    'products' => 'defaults/product.jpg',
    'users' => 'defaults/user.jpg',
  ],

  /*
    |--------------------------------------------------------------------------
    | Disk Configuration
    |--------------------------------------------------------------------------
    |
    | Specify the disk where images are stored. This can be 'public', 'local',
    | or any other disk defined in your Laravel filesystem configuration.
    |
    */
  'disk' => 'public',

  /*
    |--------------------------------------------------------------------------
    | Route Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the routes registered by this package.
    |
    */
  'routes' => [
    'enabled' => true,
    'prefix' => 'glide',
    'middleware' => ['web', 'throttle:60,1'],
  ],

  /*
    |--------------------------------------------------------------------------
    | Image Presets
    |--------------------------------------------------------------------------
    |
    | Predefined presets to facilitate consistent image usage
    | throughout the application.
    |
    */
  'presets' => [
    'large' => ['dimensions' => '800', 'format' => 'webp', 'fit' => 'max'],
    'medium' => ['dimensions' => '400', 'format' => 'webp', 'fit' => 'max'],
    'social' => ['dimensions' => '1200x630', 'format' => 'jpg', 'fit' => 'crop'],
    'thumbnail' => ['dimensions' => '150x150', 'format' => 'webp', 'fit' => 'crop'],
  ],
];
