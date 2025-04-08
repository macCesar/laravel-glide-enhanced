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
   * @param string $type Image type identifier (default, main, thumbnail, etc.)
   * @param string|null $dimensions Format: '300' or '300x200' (width x height)
   * @param string $format Output format (jpg, png, webp, pjpg)
   * @param string|null $watermark Watermark to apply
   * @param array $extraParams Additional parameters for Glide
   * @return string URL of the processed image
   */
  public function getImageUrl($type = 'default', $dimensions = null, $format = 'pjpg', $watermark = null, $extraParams = [])
  {
    // Get the image path from the model property
    $imagePath = $this->getImagePath($type);

    // If there's no image, return a default image
    if (empty($imagePath)) {
      return $this->getDefaultImageUrl($type);
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
   * @param string $type Image type identifier (default, main, thumbnail, etc.)
   * @return string|null
   */
  protected function getImagePath($type = 'default')
  {
    // Check if the model has a specific method for the type
    $method = 'get' . Str::studly($type) . 'ImagePath';
    if (method_exists($this, $method)) {
      return $this->{$method}();
    }

    // Check if the model has a property with the type name
    if (isset($this->{$type})) {
      return $this->{$type};
    }

    // Backward compatibility with previous names (to avoid breaking existing code)
    if ($type === 'default') {
      if (isset($this->image)) {
        return $this->image;
      } elseif (isset($this->imagen)) {
        return $this->imagen;
      } elseif (isset($this->foto)) {
        return $this->foto;
      }
    }

    return null;
  }

  /**
   * Get the default URL for when there's no image
   *
   * @param string $type Image type identifier (default, main, thumbnail, etc.)
   * @return string
   */
  protected function getDefaultImageUrl($type = 'default')
  {
    // Check if there's a specific method for the type
    $method = 'getDefault' . Str::studly($type) . 'ImageUrl';
    if (method_exists($this, $method)) {
      return $this->{$method}();
    }

    // Get default image configuration by type
    $defaults = config('images.defaults', []);
    if (isset($defaults[$type])) {
      return asset('img/' . $defaults[$type]);
    }

    // General default value
    return asset('img/defaults/no-image.jpg');
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
  protected function prepareImageParams($dimensions, $format, $watermark, $extraParams)
  {
    $params = [];

    // Handle dimensions (width/height)
    if (!empty($dimensions)) {
      if (strpos($dimensions, 'x') !== false) {
        list($width, $height) = explode('x', $dimensions);
        $params['w'] = $width;
        $params['h'] = $height;
      } else {
        $params['w'] = $dimensions;
      }
    }

    // Handle format
    if (!empty($format)) {
      $params['fm'] = $format;
    }

    // Handle watermark
    if (!empty($watermark)) {
      $params['mark'] = $watermark;
    }

    // Merge additional parameters
    return array_merge($params, $extraParams);
  }

  /**
   * Generate the final URL for the processed image
   *
   * @param string $path
   * @param array $params
   * @return string
   */
  protected function generateImageUrl($path, $params)
  {
    // If the path is already a complete URL, we can't process it
    if (Str::startsWith($path, ['http://', 'https://'])) {
      return $path;
    }

    // If the path starts with 'storage/', adjust it
    if (Str::startsWith($path, 'storage/')) {
      $path = substr($path, strlen('storage/'));
      return url('/img/storage/' . $path) . '?' . http_build_query($params);
    }

    // Use the image processor
    return url('/img/storage/' . $path) . '?' . http_build_query($params);
  }

  /**
   * Generates a URL for a responsive WebP image
   *
   * @param string $type Image type identifier (default, main, thumbnail, etc.)
   * @param int $width Desired width
   * @param int|null $height Desired height (optional)
   * @param string $fit Fit method (crop, contain, max, fill)
   * @param int $quality Image quality (0-100)
   * @return string
   */
  public function getImageWebpUrl($type = 'default', $width, $height = null, $fit = 'max', $quality = 85)
  {
    $imagePath = $this->getImagePath($type);

    if (empty($imagePath)) {
      return $this->getDefaultImageUrl($type);
    }

    return ImageProcessor::webpUrl($imagePath, $width, $height, $fit, $quality);
  }

  /**
   * Applies predefined manipulations to images
   *
   * @param string $type Image type identifier (default, main, thumbnail, etc.)
   * @param string $preset Preset name (thumbnail, medium, large, etc)
   * @return string URL of the processed image
   */
  public function getImagePreset($type = 'default', $preset = 'thumbnail')
  {
    $imagePath = $this->getImagePath($type);

    if (empty($imagePath)) {
      return $this->getDefaultImageUrl($type);
    }

    return ImageProcessor::preset($imagePath, $preset);
  }
}
