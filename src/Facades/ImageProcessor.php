<?php

namespace MacCesar\LaravelGlideEnhanced\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string url(string $path, array $params = [])
 * @method static string webpUrl(string $path, int $width, ?int $height = null, string $fit = 'max', int $quality = 85)
 * @method static string preset(string $path, string $preset = 'thumbnail')
 * 
 * @see \MacCesar\LaravelGlideEnhanced\ImageProcessor
 */
class ImageProcessor extends Facade
{
  /**
   * Get the registered name of the component.
   *
   * @return string
   */
  protected static function getFacadeAccessor()
  {
    return 'image-processor';
  }
}
