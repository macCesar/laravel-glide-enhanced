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
    'lifetime' => 30, // days
    'path' => 'cache/img',
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
    | Route Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the routes registered by this package.
    |
    */
  'routes' => [
    'enabled' => true,
    'prefix' => 'img',
    'middleware' => ['web'],
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
