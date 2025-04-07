<?php

return [
  /*
    |--------------------------------------------------------------------------
    | Default Images
    |--------------------------------------------------------------------------
    |
    | Configuration of default images by category.
    | These are used when a requested image is not found.
    |
    */
  'defaults' => [
    'default' => 'defaults/no-image.jpg',
    'products' => 'defaults/product.jpg',
    'users' => 'defaults/user.jpg',
    'evidence' => 'defaults/evidence.jpg',
    'documents' => 'defaults/document.jpg',
  ],

  /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for processed image cache.
    |
    */
  'cache' => [
    'path' => 'cache/img',
    'lifetime' => 30, // days
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
    'thumbnail' => ['dimensions' => '150x150', 'format' => 'webp', 'fit' => 'crop'],
    'medium' => ['dimensions' => '400', 'format' => 'webp', 'fit' => 'max'],
    'large' => ['dimensions' => '800', 'format' => 'webp', 'fit' => 'max'],
    'social' => ['dimensions' => '1200x630', 'format' => 'jpg', 'fit' => 'crop'],
  ],
];
