<?php

namespace MacCesar\LaravelGlideEnhanced\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string url(string $path, array $params = [])
 * @method static string webpUrl(string $path, array $params = [])
 * @method static string preset(string $path, string $preset = 'thumbnail')
 * @method static string srcset(string $path, array $params = [], int $maxFactor = 3)
 * 
 * @see \MacCesar\LaravelGlideEnhanced\ImageProcessor
 */
class Img extends Facade
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