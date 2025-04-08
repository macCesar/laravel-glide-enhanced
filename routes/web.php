<?php

use Illuminate\Support\Facades\Route;
use MacCesar\LaravelGlideEnhanced\Http\Controllers\ImageController;

/*
|--------------------------------------------------------------------------
| Image Processing Routes
|--------------------------------------------------------------------------
|
| This route captures any request that starts with /img/ and
| redirects it to the image controller for processing.
|
*/

Route::get('/img/{path}', [ImageController::class, 'show'])
  ->where('path', '.*')
  ->name('images.show');
