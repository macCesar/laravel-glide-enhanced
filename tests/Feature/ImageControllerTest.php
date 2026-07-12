<?php

namespace MacCesar\LaravelGlideEnhanced\Tests\Feature;

use Illuminate\Support\Facades\Storage;
use MacCesar\LaravelGlideEnhanced\Tests\TestCase;

class ImageControllerTest extends TestCase
{
  protected function setUp(): void
  {
    parent::setUp();
    $this->withoutMiddleware();
    Storage::fake('public');
    Storage::fake('local');
    $this->putImage('images/products/photo.png');
    $this->putImage('images/defaults/no-image.jpg', 'jpeg');
    $this->putImage('watermarks/logo.png');
  }

  public function test_public_route_has_default_throttle_middleware(): void
  {
    $middleware = app('router')->getRoutes()->getByName('images.show')->gatherMiddleware();

    $this->assertContains('web', $middleware);
    $this->assertContains('throttle:60,1', $middleware);
  }

  public function test_it_streams_only_decodable_images_from_the_source_root(): void
  {
    Storage::disk('public')->put('images/page.html', '<script>alert(1)</script>');
    Storage::disk('public')->put('outside.png', Storage::disk('public')->get('images/products/photo.png'));

    $response = $this->get('/glide/products/photo.png')
      ->assertOk()
      ->assertHeader('Content-Type', 'image/png')
      ->assertHeader('X-Content-Type-Options', 'nosniff')
      ->assertHeader('ETag')
      ->assertHeader('Last-Modified');

    $this->withHeader('If-None-Match', $response->headers->get('ETag'))
      ->get('/glide/products/photo.png')
      ->assertNotModified();

    $this->get('/glide/page.html')->assertNotFound();
    $this->get('/glide/outside.png')->assertOk(); // Uses the configured fallback, never outside.png.
  }

  public function test_it_rejects_traversal_and_encoded_traversal(): void
  {
    $this->get('/glide/../outside.png')->assertNotFound();
    $this->get('/glide/%252e%252e/outside.png')->assertNotFound();
    $this->get('/glide/products%5Cphoto.png')->assertNotFound();
  }

  public function test_it_validates_parameters_and_resource_limits(): void
  {
    $headers = ['Accept' => 'application/json'];

    $this->get('/glide/products/photo.png?unknown=1', $headers)->assertUnprocessable();
    $this->get('/glide/products/photo.png?w=4097', $headers)->assertUnprocessable();
    $this->get('/glide/products/photo.png?w=4096&h=4096&dpr=2', $headers)->assertUnprocessable();
    $this->get('/glide/products/photo.png?q=101', $headers)->assertUnprocessable();
    $this->get('/glide/products/photo.png?fm=svg', $headers)->assertUnprocessable();
    $this->assertSame([], Storage::disk('local')->allFiles('cache/glide'));
  }

  public function test_equivalent_parameters_share_a_cache_entry(): void
  {
    $this->get('/glide/products/photo.png?w=10&q=80')
      ->assertOk()
      ->assertHeader('X-Image-Cache', 'MISS');
    $this->get('/glide/products/photo.png?q=80&w=10')
      ->assertOk()
      ->assertHeader('X-Image-Cache', 'HIT');

    $this->assertCount(1, Storage::disk('local')->allFiles('cache/glide'));
  }

  public function test_different_sources_never_share_a_cache_entry(): void
  {
    $this->putImage('images/products/second.png');

    $this->get('/glide/products/photo.png?w=10')->assertOk();
    $this->get('/glide/products/second.png?w=10')->assertOk();

    $this->assertCount(2, Storage::disk('local')->allFiles('cache/glide'));
  }

  public function test_it_validates_and_isolates_watermarks(): void
  {
    $this->get('/glide/products/photo.png?w=10&mark=logo.png')
      ->assertOk()
      ->assertHeader('X-Image-Cache', 'MISS');
    $this->get('/glide/products/photo.png?w=10&mark=../images/products/photo.png', ['Accept' => 'application/json'])
      ->assertUnprocessable();
    $this->get('/glide/products/photo.png?w=10&mark=missing.png', ['Accept' => 'application/json'])
      ->assertUnprocessable();
  }

  public function test_missing_images_use_the_same_processing_flow_for_fallbacks(): void
  {
    $this->get('/glide/missing/photo.png?w=10')
      ->assertOk()
      ->assertHeader('X-Image-Cache', 'MISS')
      ->assertHeader('Content-Type', 'image/jpeg');
  }

  private function putImage(string $path, string $format = 'png'): void
  {
    $image = imagecreatetruecolor(20, 20);
    ob_start();
    $format === 'jpeg' ? imagejpeg($image) : imagepng($image);
    $contents = ob_get_clean();
    imagedestroy($image);
    Storage::disk('public')->put($path, $contents);
  }
}
