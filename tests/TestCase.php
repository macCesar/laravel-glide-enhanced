<?php

namespace MacCesar\LaravelGlideEnhanced\Tests;

use Illuminate\Filesystem\FilesystemServiceProvider;
use MacCesar\LaravelGlideEnhanced\ImageProcessorServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
  protected function getPackageProviders($app): array
  {
    return [
      FilesystemServiceProvider::class,
      ImageProcessorServiceProvider::class,
    ];
  }

  protected function defineEnvironment($app): void
  {
    $app['config']->set('app.key', 'base64:' . base64_encode(str_repeat('a', 32)));
    $app['config']->set('filesystems.default', 'local');
    $app['config']->set('laravel-glide.driver', 'gd');
  }
}
