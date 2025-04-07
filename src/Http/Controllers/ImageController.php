<?php

namespace MacCesar\LaravelGlideEnhanced\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Glide\GlideImage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ImageController
{
  /**
   * Display an image with dynamic processing
   *
   * @param Request $request
   * @param string $path
   * @return Response
   */
  public function show(Request $request, $path)
  {
    // If the path starts with "storage/", adjust the search to use the 'public' disk
    $disk = 'local';
    if (strpos($path, 'storage/') === 0) {
      $path = substr($path, strlen('storage/'));
      $disk = 'public';
    }

    // Check if the image exists on the appropriate disk
    if (!Storage::disk($disk)->exists($path)) {
      // Try to get a default image based on the file type
      $defaultImage = $this->getDefaultImageByPath($path);
      if ($defaultImage) {
        return $this->processDefaultImage($defaultImage, $request);
      }

      throw new NotFoundHttpException("Image not found: {$path}");
    }

    // If there are no manipulation parameters, serve the original image
    if (empty($request->all())) {
      return $this->serveImage(Storage::disk($disk)->path($path));
    }

    // Create a unique hash for this combination of image and parameters
    $paramsHash = md5(json_encode($request->all()));
    $cachePath = "cache/img/{$disk}/" . dirname($path) . '/' . $paramsHash . '_' . basename($path);

    // Check if it exists in cache
    if (Storage::exists($cachePath)) {
      return $this->serveImage(Storage::path($cachePath), true);
    }

    // Prepare cache directory
    $cacheDirectory = dirname(Storage::path($cachePath));
    if (!file_exists($cacheDirectory)) {
      mkdir($cacheDirectory, 0755, true);
    }

    try {
      // Get the full path of the image from the appropriate disk
      $sourcePath = Storage::disk($disk)->path($path);

      // Manipulate the image with spatie/laravel-glide
      GlideImage::create($sourcePath)
        ->modify($request->all())
        ->save(Storage::path($cachePath));
    } catch (\Exception $e) {
      Log::error("Error manipulating image: " . $e->getMessage(), [
        'path' => $path,
        'params' => $request->all()
      ]);
      throw $e;
    }

    return $this->serveImage(Storage::path($cachePath), false);
  }

  /**
   * Serves an image to the browser
   */
  protected function serveImage($imagePath, $fromCache = null)
  {
    if (!file_exists($imagePath)) {
      throw new NotFoundHttpException("Image not found in file system");
    }

    $response = new Response(file_get_contents($imagePath), 200);
    $response->headers->set('Content-Type', mime_content_type($imagePath));
    $response->headers->set('Cache-Control', 'public, max-age=31536000');

    if ($fromCache !== null) {
      $response->headers->set('X-Image-Cache', $fromCache ? 'HIT' : 'MISS');
    }

    return $response;
  }

  /**
   * Gets a default image based on the requested path
   *
   * @param string $path Requested image path
   * @return array|null Default image information or null if none
   */
  protected function getDefaultImageByPath($path)
  {
    // Extract information from the path to determine image type
    $pathParts = explode('/', $path);
    $category = count($pathParts) > 0 ? $pathParts[0] : '';

    // Try to get category mappings from configuration
    // With fallback to an empty array if it doesn't exist
    $defaultImages = Config::get('images.defaults', [
      'default' => 'defaults/no-image.jpg'
    ]);

    // First try with the specific category
    if (array_key_exists($category, $defaultImages)) {
      $defaultPath = $defaultImages[$category];
    }
    // Then try with a generic image
    else if (array_key_exists('default', $defaultImages)) {
      $defaultPath = $defaultImages['default'];
    }
    // Finally use a hardcoded fallback as a last resort
    else {
      $defaultPath = 'defaults/no-image.jpg';
    }

    // Look for the image on the public disk first
    if (Storage::disk('public')->exists($defaultPath)) {
      return ['disk' => 'public', 'path' => $defaultPath];
    }

    // If it doesn't exist in public, look on the local disk
    if (Storage::exists($defaultPath)) {
      return ['disk' => 'local', 'path' => $defaultPath];
    }

    return null;
  }

  /**
   * Processes and serves a default image
   *
   * @param array $defaultImage Default image information
   * @param Request $request Current request
   * @return Response HTTP response with the image
   */
  protected function processDefaultImage($defaultImage, $request)
  {
    $disk = $defaultImage['disk'];
    $path = $defaultImage['path'];

    // If there are no manipulation parameters, serve the original image
    if (empty($request->all())) {
      return $this->serveImage(Storage::disk($disk)->path($path));
    }

    // Create a unique hash for this combination of image and parameters
    $paramsHash = md5(json_encode($request->all()));
    $cachePath = "cache/img/defaults/" . $paramsHash . '_' . basename($path);

    // Check if it exists in cache
    if (Storage::exists($cachePath)) {
      return $this->serveImage(Storage::path($cachePath), true);
    }

    // Prepare cache directory
    $cacheDirectory = dirname(Storage::path($cachePath));
    if (!file_exists($cacheDirectory)) {
      mkdir($cacheDirectory, 0755, true);
    }

    try {
      // Get the full path of the image from the appropriate disk
      $sourcePath = Storage::disk($disk)->path($path);

      // Manipulate the image with spatie/laravel-glide
      GlideImage::create($sourcePath)
        ->modify($request->all())
        ->save(Storage::path($cachePath));
    } catch (\Exception $e) {
      Log::error("Error manipulating default image: " . $e->getMessage(), [
        'path' => $path,
        'params' => $request->all()
      ]);
      // In case of error with the default image, we try to serve the original
      return $this->serveImage(Storage::disk($disk)->path($path));
    }

    return $this->serveImage(Storage::path($cachePath), false);
  }
}
