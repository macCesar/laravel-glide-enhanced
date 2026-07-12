<?php

namespace MacCesar\LaravelGlideEnhanced\Tests\Feature;

use Illuminate\Support\Facades\Storage;
use MacCesar\LaravelGlideEnhanced\Tests\TestCase;

class CleanImageCacheTest extends TestCase
{
  protected function setUp(): void
  {
    parent::setUp();
    Storage::fake('local');
  }

  public function test_it_uses_configured_lifetime_when_days_is_omitted(): void
  {
    config()->set('images.cache.lifetime', 7);
    $this->cacheFile('old.jpg', 8);
    $this->cacheFile('new.jpg', 2);

    $this->artisan('images:clean-cache')->assertSuccessful();

    Storage::disk('local')->assertMissing('cache/glide/old.jpg');
    Storage::disk('local')->assertExists('cache/glide/new.jpg');
  }

  public function test_zero_deletes_everything_and_invalid_values_fail(): void
  {
    $this->cacheFile('image.jpg', 0);
    $this->artisan('images:clean-cache', ['--days' => '0'])->assertSuccessful();
    Storage::disk('local')->assertMissing('cache/glide');

    $this->artisan('images:clean-cache', ['--days' => '-1'])->assertFailed();
    $this->artisan('images:clean-cache', ['--days' => 'nope'])->assertFailed();
  }

  private function cacheFile(string $name, int $daysOld): void
  {
    Storage::disk('local')->put('cache/glide/' . $name, 'image');
    touch(Storage::disk('local')->path('cache/glide/' . $name), now()->subDays($daysOld)->getTimestamp());
  }
}
