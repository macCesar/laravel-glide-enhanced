<?php

namespace MacCesar\LaravelGlideEnhanced\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use MacCesar\LaravelGlideEnhanced\Facades\ImageProcessor;

trait HasImages
{
  /**
   * Generates the URL for a processed image associated with this model
   *
   * @param string|null $dimensions Format: '300' or '300x200' (width x height)
   * @param string $format Output format (jpg, png, webp, pjpg)
   * @param string|null $watermark Watermark to apply
   * @param array $extraParams Additional parameters for Glide
   * @return string URL of the processed image
   */
  public function fotoUrl($dimensions = null, $format = 'pjpg', $watermark = null, $extraParams = [])
  {
    // Get the image path from the model property
    $imagePath = $this->getFotoPath();

    // If there's no image, return a default image
    if (empty($imagePath)) {
      return $this->getDefaultImageUrl();
    }

    // If there are no dimensions, return the original URL
    if (empty($dimensions)) {
      return $this->getOriginalImageUrl($imagePath);
    }

    // Prepare the Glide parameters
    $params = $this->prepareImageParams($dimensions, $format, $watermark, $extraParams);

    // Generate and return the URL
    return $this->generateImageUrl($imagePath, $params);
  }

  /**
   * Get the image path for this model (customizable in each model)
   *
   * @return string|null
   */
  protected function getFotoPath()
  {
    // Default implementation - override in specific models if necessary
    if (isset($this->foto)) {
      return $this->foto;
    } elseif (isset($this->imagen)) {
      return $this->imagen;
    } elseif (isset($this->image)) {
      return $this->image;
    }

    return null;
  }

  /**
   * Get the default URL for when there's no image
   *
   * @return string
   */
  protected function getDefaultImageUrl()
  {
    // Override in specific models if necessary
    return asset('img/no-image.jpg');
  }

  /**
   * Get the original URL of the image without processing
   *
   * @param string $path
   * @return string
   */
  protected function getOriginalImageUrl($path)
  {
    // If the path is already a complete URL
    if (Str::startsWith($path, ['http://', 'https://'])) {
      return $path;
    }

    // If the path starts with 'storage/'
    if (Str::startsWith($path, 'storage/')) {
      return asset($path);
    }

    // If the path doesn't specify storage, we assume it's in storage
    return asset('storage/' . $path);
  }

  /**
   * Prepare the parameters for image processing
   *
   * @param string $dimensions
   * @param string $format
   * @param string|null $watermark
   * @param array $extraParams
   * @return array
   */
  protected function prepareImageParams($dimensions, $format, $watermark, array $extraParams)
  {
    $params = $extraParams;

    // Interpret dimensions (300 or 300x200)
    if (strpos($dimensions, 'x') !== false) {
      list($width, $height) = explode('x', $dimensions);
      $params['w'] = (int)$width;
      $params['h'] = (int)$height;
      $params['fit'] = $params['fit'] ?? 'crop';
    } else {
      $params['w'] = (int)$dimensions;
      $params['fit'] = $params['fit'] ?? 'max';
    }

    // Format
    if ($format) {
      $params['fm'] = $format;

      // For JPEG and PJPG, set default quality if not specified
      if (in_array($format, ['jpg', 'pjpg']) && !isset($params['q'])) {
        $params['q'] = 85;
      }
    }

    // Watermark (if enabled)
    if ($watermark) {
      $params['mark'] = $watermark;
      if (!isset($params['markw'])) {
        $params['markw'] = 50; // Watermark width as % of the image
      }
      if (!isset($params['markalpha'])) {
        $params['markalpha'] = 60; // Transparency (0-100)
      }
      if (!isset($params['markpos'])) {
        $params['markpos'] = 'bottom-right'; // Position
      }
    }

    return $params;
  }

  /**
   * Generate the final URL for the processed image
   *
   * @param string $path
   * @param array $params
   * @return string
   */
  protected function generateImageUrl($path, array $params)
  {
    // Make sure the path doesn't start with /
    $path = ltrim($path, '/');

    // If it doesn't start with storage/, add it
    if (!Str::startsWith($path, 'storage/')) {
      $path = 'storage/' . $path;
    }

    // Build the URL with the parameters
    $url = url('/img/' . $path);

    if (!empty($params)) {
      $url .= '?' . http_build_query($params);
    }

    return $url;
  }

  /**
   * Generates a URL for a responsive WebP image
   *
   * @param int $width Desired width
   * @param int|null $height Desired height (optional)
   * @param string $fit Fit method (crop, contain, max, fill)
   * @param int $quality Image quality (0-100)
   * @return string
   */
  public function webpUrl($width, $height = null, $fit = 'max', $quality = 85)
  {
    $dimensions = $height ? "{$width}x{$height}" : (string)$width;
    return $this->fotoUrl($dimensions, 'webp', null, [
      'fit' => $fit,
      'q' => $quality
    ]);
  }

  /**
   * Applies predefined manipulations to images
   *
   * @param string $preset Preset name (thumbnail, medium, large, etc)
   * @return string URL of the processed image
   */
  public function imagePreset($preset = 'thumbnail')
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

    return $this->fotoUrl(
      $config['dimensions'],
      $config['format'] ?? 'webp',
      null,
      ['fit' => $config['fit'] ?? 'max']
    );
  }
}
