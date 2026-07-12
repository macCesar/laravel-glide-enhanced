<?php

namespace MacCesar\LaravelGlideEnhanced\Tests\Unit;

use MacCesar\LaravelGlideEnhanced\ImageProcessor;
use MacCesar\LaravelGlideEnhanced\Tests\TestCase;
use MacCesar\LaravelGlideEnhanced\Traits\HasImages;

class ImageProcessorTest extends TestCase
{
  public function test_it_encodes_each_path_segment(): void
  {
    $url = (new ImageProcessor())->url('storage/productos/Café #1.png', ['w' => 300]);

    $this->assertSame('http://localhost/glide/productos/Caf%C3%A9%20%231.png?w=300', $url);
  }

  public function test_has_images_delegates_processed_urls_to_the_processor(): void
  {
    $model = new class {
      use HasImages;

      public string $main = 'productos/Foto uno.png';
    };

    $this->assertSame(
      'http://localhost/glide/productos/Foto%20uno.png?w=300&h=200&fm=webp',
      $model->getImageUrl('main', '300x200', 'webp')
    );
  }
}
