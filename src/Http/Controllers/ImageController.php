<?php

namespace MacCesar\LaravelGlideEnhanced\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Spatie\Glide\GlideImage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ImageController
{
  /**
   * Display an image with dynamic processing.
   */
  public function show(Request $request, string $path): Response
  {
    $relativePath = $this->normalizePath($path);
    $params = $this->validatedParams($request);
    $image = $this->findImage($relativePath);

    if ($image === null) {
      throw new NotFoundHttpException('Image not found.');
    }

    return $this->processImage($image['disk'], $image['path'], $relativePath, $params);
  }

  /**
   * @param array<string, int|string> $params
   */
  protected function processImage(string $disk, string $storagePath, string $requestedPath, array $params): Response
  {
    $sourcePath = Storage::disk($disk)->path($storagePath);
    $sourceSize = $this->assertAllowedImage($sourcePath);
    $cacheParams = $params;

    if ($params === []) {
      return $this->serveImage($sourcePath);
    }

    $this->assertResourceLimits($params, $sourceSize[0], $sourceSize[1]);

    if (isset($params['mark'])) {
      try {
        $watermarkPath = $this->rootedPath(config('images.watermark_root', 'watermarks'), (string) $params['mark']);
      } catch (NotFoundHttpException) {
        throw ValidationException::withMessages(['mark' => 'The selected watermark is invalid.']);
      }
      if (!Storage::disk($disk)->exists($watermarkPath)) {
        throw ValidationException::withMessages(['mark' => 'The selected watermark is invalid.']);
      }
      $absoluteWatermarkPath = Storage::disk($disk)->path($watermarkPath);
      $this->assertAllowedImage($absoluteWatermarkPath, 'mark');
      $params['mark'] = $absoluteWatermarkPath;
    }

    $cacheDisk = config('images.cache.disk', 'local');
    $cachePath = $this->cachePath($disk, $requestedPath, $cacheParams, $sourcePath);

    if (Storage::disk($cacheDisk)->exists($cachePath)) {
      return $this->serveImage(Storage::disk($cacheDisk)->path($cachePath), true);
    }

    Storage::disk($cacheDisk)->makeDirectory(dirname($cachePath));

    try {
      GlideImage::create($sourcePath)
        ->modify($params)
        ->save(Storage::disk($cacheDisk)->path($cachePath));
    } catch (Throwable $exception) {
      Log::warning('Image manipulation failed.', [
        'path' => $requestedPath,
        'params' => $this->safeLogParams($params),
        'exception' => $exception::class,
      ]);

      throw ValidationException::withMessages(['image' => 'The image could not be processed.']);
    }

    return $this->serveImage(Storage::disk($cacheDisk)->path($cachePath), false);
  }

  /**
   * @return array{disk: string, path: string}|null
   */
  protected function findImage(string $path): ?array
  {
    $disk = config('images.disk', 'public');
    $sourcePath = $this->rootedPath(config('images.source_root', 'images'), $path);

    if (Storage::disk($disk)->exists($sourcePath)) {
      return ['disk' => $disk, 'path' => $sourcePath];
    }

    $fallbacks = Config::get('images.fallback_images', ['default' => 'defaults/no-image.jpg']);
    $category = explode('/', $path, 2)[0];
    $fallback = $fallbacks[$category] ?? $fallbacks['default'] ?? null;
    if (!is_string($fallback)) {
      return null;
    }

    $fallbackPath = $this->rootedPath(config('images.source_root', 'images'), $this->normalizePath($fallback));

    return Storage::disk($disk)->exists($fallbackPath)
      ? ['disk' => $disk, 'path' => $fallbackPath]
      : null;
  }

  protected function normalizePath(string $path): string
  {
    $decoded = rawurldecode($path);
    if ($decoded === '' || str_contains($decoded, "\0") || str_contains($decoded, '\\')) {
      throw new NotFoundHttpException('Image not found.');
    }

    $segments = explode('/', trim($decoded, '/'));
    foreach ($segments as $segment) {
      if ($segment === '' || $segment === '.' || $segment === '..' || rawurldecode($segment) === '..') {
        throw new NotFoundHttpException('Image not found.');
      }
    }

    return implode('/', $segments);
  }

  protected function rootedPath(string $root, string $path): string
  {
    return trim($root, '/') . '/' . $this->normalizePath($path);
  }

  /**
   * @return array<string, int|string>
   */
  protected function validatedParams(Request $request): array
  {
    $params = $request->query();
    $allowed = config('images.allowed_parameters', []);
    $unknown = array_diff(array_keys($params), $allowed);

    if ($unknown !== []) {
      throw ValidationException::withMessages([
        'parameters' => 'Unknown image parameters: ' . implode(', ', $unknown) . '.',
      ]);
    }

    $rules = [
      'w' => ['integer', 'min:1', 'max:' . config('images.limits.max_width', 4096)],
      'h' => ['integer', 'min:1', 'max:' . config('images.limits.max_height', 4096)],
      'dpr' => ['numeric', 'min:0.1', 'max:' . config('images.limits.max_dpr', 4)],
      'q' => ['integer', 'between:1,100'],
      'fit' => ['string', 'in:contain,max,fill,stretch,crop,crop-center,crop-top,crop-top-left,crop-top-right,crop-bottom,crop-bottom-left,crop-bottom-right,crop-left,crop-right'],
      'fm' => ['string', 'in:jpg,jpeg,pjpg,png,webp'],
      'or' => ['string', 'in:auto,0,90,180,270'],
      'rect' => ['string', 'regex:/^\d+,\d+,\d+,\d+$/'],
      'bg' => ['string', 'regex:/^(?:[0-9a-fA-F]{3,8}|transparent)$/'],
      'border' => ['string', 'regex:/^\d+,[0-9a-fA-F]{3,8}$/'],
      'sharp' => ['integer', 'between:0,100'],
      'blur' => ['integer', 'between:0,100'],
      'gam' => ['numeric', 'between:0.1,9.99'],
      'bright' => ['integer', 'between:-100,100'],
      'con' => ['integer', 'between:-100,100'],
      'sat' => ['integer', 'between:-100,100'],
      'filt' => ['string', 'in:greyscale,sepia'],
      'mark' => ['string', 'max:512'],
      'markw' => ['integer', 'min:1', 'max:' . config('images.limits.max_width', 4096)],
      'markh' => ['integer', 'min:1', 'max:' . config('images.limits.max_height', 4096)],
      'markfit' => ['string', 'in:contain,max,fill,stretch,crop'],
      'markx' => ['integer', 'min:0', 'max:4096'],
      'marky' => ['integer', 'min:0', 'max:4096'],
      'markpad' => ['integer', 'min:0', 'max:4096'],
      'markpos' => ['string', 'in:top-left,top,top-right,left,center,right,bottom-left,bottom,bottom-right'],
      'markalpha' => ['integer', 'between:0,100'],
    ];

    $validated = validator($params, array_intersect_key($rules, $params))->validate();
    $normalized = [];
    foreach ($validated as $key => $value) {
      $normalized[$key] = is_numeric($value) ? $value + 0 : strtolower((string) $value);
    }

    $width = (float) ($normalized['w'] ?? 1) * (float) ($normalized['dpr'] ?? 1);
    $height = (float) ($normalized['h'] ?? 1) * (float) ($normalized['dpr'] ?? 1);
    if (($width * $height) > ((float) config('images.limits.max_megapixels', 16) * 1000000)) {
      throw ValidationException::withMessages(['dimensions' => 'The requested image exceeds the pixel limit.']);
    }

    ksort($normalized);

    return $normalized;
  }

  /**
   * @return array{0: int, 1: int}
   */
  protected function assertAllowedImage(string $path, string $field = 'image'): array
  {
    $details = @getimagesize($path);
    $mime = is_array($details) ? ($details['mime'] ?? null) : null;
    if (!is_string($mime) || !in_array($mime, config('images.allowed_mime_types', []), true)) {
      if ($field === 'mark') {
        throw ValidationException::withMessages(['mark' => 'The watermark must be a valid JPEG, PNG, or WebP image.']);
      }
      throw new NotFoundHttpException('Image not found.');
    }

    return [(int) $details[0], (int) $details[1]];
  }

  /**
   * @param array<string, int|string> $params
   */
  protected function assertResourceLimits(array $params, int $sourceWidth, int $sourceHeight): void
  {
    $dpr = (float) ($params['dpr'] ?? 1);
    $width = isset($params['w']) ? (float) $params['w'] : null;
    $height = isset($params['h']) ? (float) $params['h'] : null;

    if ($width === null && $height === null) {
      $width = $sourceWidth;
      $height = $sourceHeight;
    } elseif ($width === null) {
      $width = $sourceHeight > 0 ? $sourceWidth * ($height / $sourceHeight) : 0;
    } elseif ($height === null) {
      $height = $sourceWidth > 0 ? $sourceHeight * ($width / $sourceWidth) : 0;
    }

    $width *= $dpr;
    $height *= $dpr;
    if ($width > (int) config('images.limits.max_width', 4096)
      || $height > (int) config('images.limits.max_height', 4096)
      || ($width * $height) > ((float) config('images.limits.max_megapixels', 16) * 1000000)) {
      throw ValidationException::withMessages(['dimensions' => 'The requested image exceeds the resource limits.']);
    }
  }

  /**
   * @param array<string, int|string> $params
   */
  protected function cachePath(string $disk, string $path, array $params, string $sourcePath): string
  {
    $hash = hash('sha256', json_encode($params, JSON_UNESCAPED_SLASHES));
    $extension = $params['fm'] ?? pathinfo($sourcePath, PATHINFO_EXTENSION);
    if (in_array($extension, ['jpeg', 'pjpg'], true)) {
      $extension = 'jpg';
    }

    return trim(config('images.cache.path', 'cache/glide'), '/')
      . '/' . rawurlencode($disk)
      . '/' . dirname($path)
      . '/' . rawurlencode(basename($path)) . '-' . $hash . '.' . $extension;
  }

  protected function serveImage(string $imagePath, ?bool $fromCache = null): BinaryFileResponse
  {
    if (!is_file($imagePath)) {
      throw new NotFoundHttpException('Image not found.');
    }

    $response = new BinaryFileResponse($imagePath);
    $response->headers->set('Content-Type', mime_content_type($imagePath) ?: 'application/octet-stream');
    $response->headers->set('X-Content-Type-Options', 'nosniff');
    $response->setAutoEtag();
    $response->setAutoLastModified();

    $maxAge = max(0, (int) config('images.cache.headers.max_age', 86400));
    $stale = max(0, (int) config('images.cache.headers.stale_while_revalidate', 3600));
    $response->headers->set('Cache-Control', "public, max-age={$maxAge}, stale-while-revalidate={$stale}");

    if ($fromCache !== null) {
      $response->headers->set('X-Image-Cache', $fromCache ? 'HIT' : 'MISS');
    }

    $response->isNotModified(request());

    return $response;
  }

  /**
   * @param array<string, int|string> $params
   * @return array<string, int|string>
   */
  protected function safeLogParams(array $params): array
  {
    if (isset($params['mark'])) {
      $params['mark'] = basename((string) $params['mark']);
    }

    return $params;
  }
}
