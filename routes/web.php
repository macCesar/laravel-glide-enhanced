<?php

use Illuminate\Support\Facades\Route;
use MacCesar\LaravelGlideEnhanced\Http\Controllers\ImageController;

/*
|--------------------------------------------------------------------------
| Image Processing Routes
|--------------------------------------------------------------------------
|
| This route captures any request that starts with the configured prefix
| (default: /glide/) and redirects it to the image controller for processing.
|
*/

if (config('images.routes.enabled', true)) {
  Route::prefix(config('images.routes.prefix', 'glide'))
    ->middleware(config('images.routes.middleware', ['web']))
    ->group(function () {
      Route::get('/{path}', [ImageController::class, 'show'])
        ->where('path', '.*')
        ->name('images.show');
    });
}
