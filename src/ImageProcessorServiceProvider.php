<?php

namespace MacCesar\LaravelGlideEnhanced;

use MacCesar\LaravelGlideEnhanced\Console\Commands\CleanImageCache;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;

/**
 * @property-read Application $app
 */
class ImageProcessorServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    // Register the configuration file
    $this->mergeConfigFrom(
      __DIR__ . '/../config/images.php',
      'images'
    );

    // Register the main service
    $this->app->singleton('image-processor', function () {
      return new ImageProcessor();
    });
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    // Publish configuration
    $this->publishes([
      __DIR__ . '/../config/images.php' => config_path('images.php'),
    ], 'images-config');

    // Load routes
    $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

    // Register commands
    if ($this->app->runningInConsole()) {
      $this->commands([
        CleanImageCache::class,
      ]);
    }
  }
}
