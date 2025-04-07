<?php

namespace MacCesar\LaravelGlideEnhanced;

use Illuminate\Support\Str;

class ImageProcessor
{
  /**
   * Generates a URL for a processed image
   *
   * @param string $path Image path
   * @param array $params Processing parameters
   * @return string Final URL of the processed image
   */
  public function url(string $path, array $params = []): string
  {
    // Make sure the path doesn't start with /
    $path = ltrim($path, '/');

    // If it doesn't start with storage/, add it
    if (!Str::startsWith($path, 'storage/')) {
      $path = 'storage/' . $path;
    }

    // Build the URL with parameters
    $url = url('/img/' . $path);

    if (!empty($params)) {
      $url .= '?' . http_build_query($params);
    }

    return $url;
  }

  /**
   * Generates WebP URL with specific parameters
   *
   * @param string $path Image path
   * @param int $width Desired width
   * @param int|null $height Desired height (optional)
   * @param string $fit Fit method (crop, contain, max, fill)
   * @param int $quality Image quality (0-100)
   * @return string
   */
  public function webpUrl(string $path, int $width, ?int $height = null, string $fit = 'max', int $quality = 85): string
  {
    $params = [
      'w' => $width,
      'fm' => 'webp',
      'fit' => $fit,
      'q' => $quality
    ];

    if ($height !== null) {
      $params['h'] = $height;
    }

    return $this->url($path, $params);
  }

  /**
   * Applies predefined manipulations to images
   *
   * @param string $path Image path
   * @param string $preset Preset name (thumbnail, medium, large, etc.)
   * @return string URL of the processed image
   */
  public function preset(string $path, string $preset = 'thumbnail'): string
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
    $params = ['fit' => $config['fit'] ?? 'max'];

    // Format
    if (isset($config['format'])) {
      $params['fm'] = $config['format'];

      // For JPEG and PJPG, set default quality if not specified
      if (in_array($config['format'], ['jpg', 'pjpg']) && !isset($params['q'])) {
        $params['q'] = 85;
      }
    }

    // Interpret dimensions (300 or 300x200)
    if (isset($config['dimensions'])) {
      if (strpos($config['dimensions'], 'x') !== false) {
        list($width, $height) = explode('x', $config['dimensions']);
        $params['w'] = (int)$width;
        $params['h'] = (int)$height;
        $params['fit'] = $params['fit'] ?? 'crop';
      } else {
        $params['w'] = (int)$config['dimensions'];
        $params['fit'] = $params['fit'] ?? 'max';
      }
    }

    return $this->url($path, $params);
  }
}
