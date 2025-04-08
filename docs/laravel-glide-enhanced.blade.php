@php
  use MacCesar\LaravelGlideEnhanced\Facades\ImageProcessor as Img;
@endphp

<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Laravel Glide Enhanced - Demonstration</title>
    <script src="https://cdn.tailwindcss.com"></script>
  </head>

  <body class="min-h-screen bg-gray-200">
    <div class="container mx-auto px-4 py-8">
      <header class="mb-12 text-center">
        <h1 class="mb-2 text-4xl font-bold text-gray-700">Laravel Glide Enhanced - Demonstration</h1>
        <p class="text-lg text-gray-600">A powerful package for image processing and optimization</p>
        <p class="mt-2 text-sm text-gray-500">Sample image: <a class="text-gray-600 underline hover:text-gray-800" href="https://www.freepik.com/free-ai-image/mountain-lake-reflection_415577614.htm#fromView" rel="noopener noreferrer" target="_blank">Mountain Lake Reflection on Freepik</a></p>
      </header>

      <!-- Introduction Section -->
      <div class="mb-12 rounded-lg bg-white p-6 shadow-md">
        <h2 class="mb-4 text-2xl font-bold text-gray-700">About Laravel Glide Enhanced</h2>
        <p class="mb-4 text-gray-600">
          This package provides a complete solution for dynamic image processing in Laravel applications. Built on top of Spatie's Laravel Glide, it offers extended functionality including WebP conversion, predefined presets, and a powerful API for image manipulation.
        </p>
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
          <div class="rounded-lg bg-gray-50 p-4">
            <h3 class="mb-2 text-xl font-semibold text-gray-600">Key Features</h3>
            <ul class="list-inside list-disc space-y-2 text-gray-600">
              <li>Dynamic image resizing, cropping, and transformation</li>
              <li>Automatic WebP conversion for modern browsers</li>
              <li>Efficient caching system to reduce server load</li>
              <li>Predefined presets for consistent image processing</li>
              <li>Rich set of image manipulation options</li>
              <li>Eloquent model integration via traits</li>
            </ul>
          </div>
          <div class="rounded-lg bg-gray-50 p-4">
            <h3 class="mb-2 text-xl font-semibold text-gray-600">Getting Started</h3>
            <p class="mb-2 text-gray-600">Install the package via composer:</p>
            <pre class="mb-4 rounded bg-gray-100 p-2 text-sm"><code>composer require maccesar/laravel-glide-enhanced</code></pre>
            <p class="mb-2 text-gray-600">Publish the configuration:</p>
            <pre class="mb-4 rounded bg-gray-100 p-2 text-sm"><code>php artisan vendor:publish --tag=images-config</code></pre>
            <p class="mb-2 text-gray-600">Basic Usage:</p>
            <pre class="rounded bg-gray-100 p-2 text-sm"><code>use MacCesar\LaravelGlideEnhanced\Facades\ImageProcessor as Img;

// Generate a URL for an image with specific transformations
$url = Img::url('path/to/image.jpg', ['w' => 600 ])
            </code></pre>
          </div>
        </div>
        <div class="mt-6 rounded-lg bg-blue-50 p-4 text-blue-700">
          <p class="flex items-center">
            <svg class="mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path clip-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" fill-rule="evenodd" />
            </svg>
            This demonstration showcases the various image processing capabilities. Each example includes the code needed to implement it in your own application.
          </p>
        </div>
      </div>

      <h2 class="mb-6 text-2xl font-bold text-gray-700">Image Processing Examples</h2>
      <p class="mb-8 text-gray-600">The following examples demonstrate the range of image processing capabilities offered by Laravel Glide Enhanced. Each card shows a different transformation applied to the same source image.</p>

      <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
        <!-- Original Image -->
        <div class="overflow-hidden rounded-lg bg-white shadow-md" style="display: flex; flex-direction: column;">
          <div class="bg-gray-50 p-4">
            <h2 class="text-xl font-semibold text-gray-600">Original Image</h2>
          </div>
          <div class="flex items-center justify-center p-6" style="flex-grow: 1;">
            <img alt="Original" class="h-auto max-w-full" src="{{ asset('storage/mountainlake.jpg') }}">
          </div>
          <div class="bg-gray-50 px-4 py-3" style="margin-top: auto;">
            <p class="text-sm text-gray-600">Original image without modifications</p>
          </div>
        </div>

        <!-- Resized Image -->
        <div class="overflow-hidden rounded-lg bg-white shadow-md" style="display: flex; flex-direction: column;">
          <div class="bg-gray-50 p-4">
            <h2 class="text-xl font-semibold text-gray-600">Resized (300px)</h2>
          </div>
          <div class="flex items-center justify-center p-6" style="flex-grow: 1;">
            <img alt="Resized" class="h-auto max-w-full" src="{{ Img::url('mountainlake.jpg', ['w' => 600]) }}">
          </div>
          <div class="bg-gray-50 px-4 py-3" style="margin-top: auto;">
            <p class="text-sm text-gray-600">Image resized to 300px width</p>
            <code class="mt-2 block text-xs text-gray-500">@{{ Img::url('mountainlake.jpg', ['w' => 600]) }}</code>
          </div>
        </div>

        <!-- WebP Image -->
        <div class="overflow-hidden rounded-lg bg-white shadow-md" style="display: flex; flex-direction: column;">
          <div class="bg-gray-50 p-4">
            <h2 class="text-xl font-semibold text-gray-600">WebP Format</h2>
          </div>
          <div class="flex items-center justify-center p-6" style="flex-grow: 1;">
            <img alt="WebP" class="h-auto max-w-full" src="{{ Img::webpUrl('mountainlake.jpg', 300) }}">
          </div>
          <div class="bg-gray-50 px-4 py-3" style="margin-top: auto;">
            <p class="text-sm text-gray-600">Converted to WebP format for optimization</p>
            <code class="mt-2 block text-xs text-gray-500">@{{ Img::webpUrl('mountainlake.jpg', 300) }}</code>
          </div>
        </div>

        <!-- Using preset (thumbnail) -->
        <div class="overflow-hidden rounded-lg bg-white shadow-md" style="display: flex; flex-direction: column;">
          <div class="bg-gray-50 p-4">
            <h2 class="text-xl font-semibold text-gray-600">Preset: Thumbnail</h2>
          </div>
          <div class="flex items-center justify-center p-6" style="flex-grow: 1;">
            <img alt="Thumbnail" class="h-auto max-w-full" src="{{ Img::preset('mountainlake.jpg', 'thumbnail') }}">
          </div>
          <div class="bg-gray-50 px-4 py-3" style="margin-top: auto;">
            <p class="text-sm text-gray-600">Using the predefined 'thumbnail' preset</p>
            <code class="mt-2 block text-xs text-gray-500">@{{ Img::preset('mountainlake.jpg', 'thumbnail') }}</code>
          </div>
        </div>

        <!-- Image with filter -->
        <div class="overflow-hidden rounded-lg bg-white shadow-md" style="display: flex; flex-direction: column;">
          <div class="bg-gray-50 p-4">
            <h2 class="text-xl font-semibold text-gray-600">Filter: Greyscale</h2>
          </div>
          <div class="flex items-center justify-center p-6" style="flex-grow: 1;">
            <img alt="Greyscale" class="h-auto max-w-full" src="{{ Img::url('mountainlake.jpg', ['w' => 600, 'filt' => 'greyscale']) }}">
          </div>
          <div class="bg-gray-50 px-4 py-3" style="margin-top: auto;">
            <p class="text-sm text-gray-600">Image with greyscale filter</p>
            <code class="mt-2 block text-xs text-gray-500">@{{ Img::url('mountainlake.jpg', ['w' => 600, 'filt' => 'greyscale']) }}</code>
          </div>
        </div>

        <!-- Image with adjusted brightness -->
        <div class="overflow-hidden rounded-lg bg-white shadow-md" style="display: flex; flex-direction: column;">
          <div class="bg-gray-50 p-4">
            <h2 class="text-xl font-semibold text-gray-600">Increased Brightness</h2>
          </div>
          <div class="flex items-center justify-center p-6" style="flex-grow: 1;">
            <img alt="Brightness" class="h-auto max-w-full" src="{{ Img::url('mountainlake.jpg', ['w' => 600, 'bri' => 50]) }}">
          </div>
          <div class="bg-gray-50 px-4 py-3" style="margin-top: auto;">
            <p class="text-sm text-gray-600">Image with brightness increased by 50 units</p>
            <code class="mt-2 block text-xs text-gray-500">@{{ Img::url('mountainlake.jpg', ['w' => 600, 'bri' => 50]) }}</code>
          </div>
        </div>

        <!-- Medium preset -->
        <div class="overflow-hidden rounded-lg bg-white shadow-md" style="display: flex; flex-direction: column;">
          <div class="bg-gray-50 p-4">
            <h2 class="text-xl font-semibold text-gray-600">Preset: Medium</h2>
          </div>
          <div class="flex items-center justify-center p-6" style="flex-grow: 1;">
            <img alt="Medium" class="h-auto max-w-full" src="{{ Img::preset('mountainlake.jpg', 'medium') }}">
          </div>
          <div class="bg-gray-50 px-4 py-3" style="margin-top: auto;">
            <p class="text-sm text-gray-600">Preset 'medium': 400px width, WebP, proportional fit</p>
            <code class="mt-2 block text-xs text-gray-500">@{{ Img::preset('mountainlake.jpg', 'medium') }}</code>
          </div>
        </div>

        <!-- Social media preset -->
        <div class="overflow-hidden rounded-lg bg-white shadow-md" style="display: flex; flex-direction: column;">
          <div class="bg-gray-50 p-4">
            <h2 class="text-xl font-semibold text-gray-600">Preset: Social</h2>
          </div>
          <div class="flex items-center justify-center p-6" style="flex-grow: 1;">
            <img alt="Social" class="h-auto max-w-full" src="{{ Img::preset('mountainlake.jpg', 'social') }}">
          </div>
          <div class="bg-gray-50 px-4 py-3" style="margin-top: auto;">
            <p class="text-sm text-gray-600">Optimized for sharing on social media (1200x630px)</p>
            <code class="mt-2 block text-xs text-gray-500">@{{ Img::preset('mountainlake.jpg', 'social') }}</code>
          </div>
        </div>

        <!-- Sepia filter -->
        <div class="overflow-hidden rounded-lg bg-white shadow-md" style="display: flex; flex-direction: column;">
          <div class="bg-gray-50 p-4">
            <h2 class="text-xl font-semibold text-gray-600">Filter: Sepia</h2>
          </div>
          <div class="flex items-center justify-center p-6" style="flex-grow: 1;">
            <img alt="Sepia" class="h-auto max-w-full" src="{{ Img::url('mountainlake.jpg', ['w' => 600, 'filt' => 'sepia']) }}">
          </div>
          <div class="bg-gray-50 px-4 py-3" style="margin-top: auto;">
            <p class="text-sm text-gray-600">Image with sepia effect</p>
            <code class="mt-2 block text-xs text-gray-500">@{{ Img::url('mountainlake.jpg', ['w' => 600, 'filt' => 'sepia']) }}</code>
          </div>
        </div>

        <!-- Contrast adjustment -->
        <div class="overflow-hidden rounded-lg bg-white shadow-md" style="display: flex; flex-direction: column;">
          <div class="bg-gray-50 p-4">
            <h2 class="text-xl font-semibold text-gray-600">Increased Contrast</h2>
          </div>
          <div class="flex items-center justify-center p-6" style="flex-grow: 1;">
            <img alt="Contrast" class="h-auto max-w-full" src="{{ Img::url('mountainlake.jpg', ['w' => 600, 'con' => 30]) }}">
          </div>
          <div class="bg-gray-50 px-4 py-3" style="margin-top: auto;">
            <p class="text-sm text-gray-600">Image with increased contrast</p>
            <code class="mt-2 block text-xs text-gray-500">@{{ Img::url('mountainlake.jpg', ['w' => 600, 'con' => 30]) }}</code>
          </div>
        </div>

        <!-- Image with rotation -->
        {{-- <div class="overflow-hidden rounded-lg bg-white shadow-md" style="display: flex; flex-direction: column;">
          <div class="bg-gray-50 p-4">
            <h2 class="text-xl font-semibold text-gray-600">Rotation 90° (Requiere Imagick)</h2>
          </div>
          <div class="flex items-center justify-center p-6" style="flex-grow: 1;">
            <img alt="Rotated" class="h-auto max-w-full" src="{{ Img::url('mountainlake.jpg', ['w' => 600]) }}">
            <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-70 p-4 text-center text-white">
              <div>
                <p class="mb-2 text-xl font-bold">Función requiere configuración</p>
                <p>La rotación requiere el driver Imagick</p>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3" style="margin-top: auto;">
            <p class="text-sm text-gray-600">Para habilitar: Configura driver Imagick</p>
            <code class="mt-2 block text-xs text-gray-500">@{{ Img::url('mountainlake.jpg', ['w' => 600, 'rot' => 90]) }}</code>
          </div>
        </div> --}}

        <!-- Specific crop -->
        <div class="overflow-hidden rounded-lg bg-white shadow-md" style="display: flex; flex-direction: column;">
          <div class="bg-gray-50 p-4">
            <h2 class="text-xl font-semibold text-gray-600">Centered Crop</h2>
          </div>
          <div class="flex items-center justify-center p-6" style="flex-grow: 1;">
            <img alt="Cropped" class="h-auto max-w-full" src="{{ Img::url('mountainlake.jpg', ['w' => 600, 'h' => 600, 'fit' => 'crop']) }}">
          </div>
          <div class="bg-gray-50 px-4 py-3" style="margin-top: auto;">
            <p class="text-sm text-gray-600">Square 600x600 centered crop</p>
            <code class="mt-2 block text-xs text-gray-500">@{{ Img::url('mountainlake.jpg', ['w' => 600, 'h' => 600, 'fit' => 'crop']) }}</code>
          </div>
        </div>

        <!-- Watermark -->
        <div class="overflow-hidden rounded-lg bg-white shadow-md" style="display: flex; flex-direction: column;">
          <div class="bg-gray-50 p-4">
            <h2 class="text-xl font-semibold text-gray-600">Watermark</h2>
          </div>
          <div class="flex items-center justify-center p-6" style="flex-grow: 1;">
            <img alt="With watermark" class="h-auto max-w-full" src="{{ Img::url('mountainlake.jpg', ['w' => 600, 'mark' => 'watermarks/logo.png', 'markw' => 100, 'markpos' => 'bottom-left', 'markalpha' => 60]) }}">
          </div>
          <div class="bg-gray-50 px-4 py-3" style="margin-top: auto;">
            <p class="text-sm text-gray-600">Image with semi-transparent watermark</p>
            <code class="mt-2 block text-xs text-gray-500">@{{ Img::url('mountainlake.jpg', ['w' => 600, 'mark' => 'watermarks/logo.png', 'markw' => 100, 'markpos' => 'bottom-left', 'markalpha' => 60]) }}</code>
          </div>
        </div>

        <!-- Combined effects -->
        <div class="overflow-hidden rounded-lg bg-white shadow-md" style="display: flex; flex-direction: column;">
          <div class="bg-gray-50 p-4">
            <h2 class="text-xl font-semibold text-gray-600">Combined Effects</h2>
          </div>
          <div class="flex items-center justify-center p-6" style="flex-grow: 1;">
            <img alt="Multiple effects" class="h-auto max-w-full" src="{{ Img::url('mountainlake.jpg', ['w' => 600, 'filt' => 'greyscale', 'blur' => 5, 'bri' => 10]) }}">
          </div>
          <div class="bg-gray-50 px-4 py-3" style="margin-top: auto;">
            <p class="text-sm text-gray-600">Greyscale + blur + brightness</p>
            <code class="mt-2 block text-xs text-gray-500">@{{ Img::url('mountainlake.jpg', ['w' => 600, 'filt' => 'greyscale', 'blur' => 5, 'bri' => 10]) }}</code>
          </div>
        </div>
      </div>

      <div class="mt-12 rounded-lg bg-white p-6 shadow-md">
        <h2 class="mb-4 text-2xl font-bold text-gray-700">Practical Example: Responsive</h2>
        <p class="mb-4 text-gray-600">Implementing responsive images is essential for modern web development. Laravel Glide Enhanced makes it easy to generate appropriately sized images for different devices and screen resolutions.</p>
        <p class="mb-6 text-gray-600">These examples show how to implement responsive images using HTML5's picture element and srcset attribute, combined with Laravel Glide Enhanced's powerful API.</p>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2" style="display: grid; grid-auto-rows: 1fr;">
          <!-- Responsive Image with srcset -->
          <div class="overflow-hidden rounded-lg bg-white shadow-md" style="display: flex; flex-direction: column;">
            <div class="bg-gray-50 p-4">
              <h2 class="text-xl font-semibold text-gray-600">Responsive Image with srcset</h2>
            </div>
            <div class="flex items-center justify-center p-6" style="flex-grow: 1;">
              <img alt="Responsive with pixel density" src="{{ Img::url('mountainlake.jpg', ['w' => 600]) }}" srcset="{{ Img::url('mountainlake.jpg', ['w' => 600]) }} 1x, {{ Img::url('mountainlake.jpg', ['w' => 1200]) }} 2x, {{ Img::url('mountainlake.jpg', ['w' => 1800]) }} 3x">
            </div>
            <div class="bg-gray-50 px-4 py-3" style="margin-top: auto;">
              <p class="text-sm text-gray-600">Image with pixel density variants (1x, 2x, 3x)</p>
              <code class="mt-2 block text-xs text-gray-500">src="@{{ Img::url('mountainlake.jpg', ['w' => 600]) }}" srcset="@{{ Img::url('mountainlake.jpg', ['w' => 600]) }} 1x, @{{ Img::url('mountainlake.jpg', ['w' => 1200]) }} 2x, @{{ Img::url('mountainlake.jpg', ['w' => 1800]) }} 3x"</code>
            </div>
          </div>

          <!-- NEW: Simplified srcset method -->
          <div class="overflow-hidden rounded-lg bg-white shadow-md" style="display: flex; flex-direction: column;">
            <div class="bg-gray-50 p-4">
              <h2 class="text-xl font-semibold text-gray-600">Simplified srcset Method</h2>
            </div>
            <div class="flex items-center justify-center p-6" style="flex-grow: 1;">
              <img alt="Auto-generated srcset" src="{{ Img::url('mountainlake.jpg', ['w' => 600, 'fm' => 'webp']) }}" srcset="{{ Img::srcset('mountainlake.jpg', ['w' => 600, 'fm' => 'webp']) }}">
            </div>
            <div class="bg-gray-50 px-4 py-3" style="margin-top: auto;">
              <p class="text-sm text-gray-600">Auto-generated srcset with the new srcset method</p>
              <code class="mt-2 block text-xs text-gray-500">srcset="@{{ Img::srcset('mountainlake.jpg', ['w' => 600, 'fm' => 'webp']) }}"</code>
            </div>
          </div>

          <!-- Picture element -->
          <div class="overflow-hidden rounded-lg bg-white shadow-md" style="display: flex; flex-direction: column;">
            <div class="bg-gray-50 p-4">
              <h2 class="text-xl font-semibold text-gray-600">Picture Element</h2>
            </div>
            <div class="flex items-center justify-center p-6" style="flex-grow: 1;">
              <picture>
                <source media="(min-width: 1920px)" srcset="{{ Img::webpUrl('mountainlake.jpg', 1600) }} 1x, {{ Img::webpUrl('mountainlake.jpg', 3200) }} 2x">
                <source media="(min-width: 1024px)" srcset="{{ Img::webpUrl('mountainlake.jpg', 1200) }} 1x, {{ Img::webpUrl('mountainlake.jpg', 2400) }} 2x">
                <source media="(min-width: 768px)" srcset="{{ Img::webpUrl('mountainlake.jpg', 800) }} 1x, {{ Img::webpUrl('mountainlake.jpg', 1600) }} 2x">
                <source media="(min-width: 640px)" srcset="{{ Img::webpUrl('mountainlake.jpg', 600) }} 1x, {{ Img::webpUrl('mountainlake.jpg', 1200) }} 2x">
                <img alt="Responsive image" class="h-auto max-w-full" src="{{ Img::webpUrl('mountainlake.jpg', 400) }}" style="max-width: 1200px;">
              </picture>
            </div>
            <div class="bg-gray-50 px-4 py-3" style="margin-top: auto;">
              <p class="text-sm text-gray-600">This image automatically adapts to the screen size and pixel density</p>
              <code class="mt-2 block text-xs text-gray-500">
                &lt;picture&gt;<br />
                &lt;source media="(min-width: 1920px)" srcset="@{{ Img::webpUrl('mountainlake.jpg', 1600) }} 1x, @{{ Img::webpUrl('mountainlake.jpg', 3200) }} 2x"&gt;<br />
                &lt;source media="(min-width: 1024px)" srcset="@{{ Img::webpUrl('mountainlake.jpg', 1200) }} 1x, @{{ Img::webpUrl('mountainlake.jpg', 2400) }} 2x"&gt;<br />
                &lt;source media="(min-width: 768px)" srcset="@{{ Img::webpUrl('mountainlake.jpg', 800) }} 1x, @{{ Img::webpUrl('mountainlake.jpg', 1600) }} 2x"&gt;<br />
                &lt;source media="(min-width: 640px)" srcset="@{{ Img::webpUrl('mountainlake.jpg', 600) }} 1x, @{{ Img::webpUrl('mountainlake.jpg', 1200) }} 2x"&gt;<br />
                &lt;img src="@{{ Img::webpUrl('mountainlake.jpg', 400) }}" alt="Responsive image" style="max-width: 1200px;"&gt;<br />
                &lt;/picture&gt;
              </code>
            </div>
          </div>

          <!-- Image with density descriptors -->
          <div class="overflow-hidden rounded-lg bg-white shadow-md" style="display: flex; flex-direction: column;">
            <div class="bg-gray-50 p-4">
              <h2 class="text-xl font-semibold text-gray-600">Image with density descriptors (1x, 2x, 3x)</h2>
            </div>
            <div class="flex items-center justify-center p-6" style="flex-grow: 1;">
              <img alt="Image with variable pixel density" class="h-auto max-w-full" src="{{ Img::url('mountainlake.jpg', ['w' => 600]) }}" srcset="{{ Img::url('mountainlake.jpg', ['w' => 600]) }} 1x, 
                       {{ Img::url('mountainlake.jpg', ['w' => 1200]) }} 2x, 
                       {{ Img::url('mountainlake.jpg', ['w' => 1800]) }} 3x">
            </div>
            <div class="bg-gray-50 px-4 py-3" style="margin-top: auto;">
              <p class="text-sm text-gray-600">The appropriate version will be displayed based on the device's pixel density</p>
              <code class="mt-2 block text-xs text-gray-500">
                &lt;img srcset="@{{ Img::url('mountainlake.jpg', ['w' => 600]) }} 1x, 
                       @{{ Img::url('mountainlake.jpg', ['w' => 1200]) }} 2x, 
                       @{{ Img::url('mountainlake.jpg', ['w' => 1800]) }} 3x"
                src="@{{ Img::url('mountainlake.jpg', ['w' => 600]) }}"
                alt="Image with variable pixel density"&gt;
              </code>
            </div>
          </div>

          <!-- Optimal combination: WebP + pixel density -->
          <div class="overflow-hidden rounded-lg bg-white shadow-md" style="display: flex; flex-direction: column;">
            <div class="bg-gray-50 p-4">
              <h2 class="text-xl font-semibold text-gray-600">Optimal combination: WebP + pixel density</h2>
            </div>
            <div class="flex items-center justify-center p-6" style="flex-grow: 1;">
              <img alt="WebP with different densities" class="h-auto max-w-full" src="{{ Img::url('mountainlake.jpg', ['w' => 600, 'fm' => 'jpg']) }}" srcset="{{ Img::url('mountainlake.jpg', ['w' => 600, 'fm' => 'webp']) }} 1x, 
                       {{ Img::url('mountainlake.jpg', ['w' => 1200, 'fm' => 'webp']) }} 2x, 
                       {{ Img::url('mountainlake.jpg', ['w' => 1800, 'fm' => 'webp']) }} 3x">
            </div>
            <div class="bg-gray-50 px-4 py-3" style="margin-top: auto;">
              <p class="text-sm text-gray-600">Optimized WebP format with different resolutions based on device</p>
              <code class="mt-2 block text-xs text-gray-500">
                &lt;img srcset="@{{ Img::url('mountainlake.jpg', ['w' => 600, 'fm' => 'webp']) }} 1x, 
                       @{{ Img::url('mountainlake.jpg', ['w' => 1200, 'fm' => 'webp']) }} 2x, 
                       @{{ Img::url('mountainlake.jpg', ['w' => 1800, 'fm' => 'webp']) }} 3x"
                src="@{{ Img::url('mountainlake.jpg', ['w' => 600, 'fm' => 'jpg']) }}"
                alt="WebP with different densities"&gt;
              </code>
            </div>
          </div>

          <!-- Optimization with DPR parameter -->
          <div class="overflow-hidden rounded-lg bg-white shadow-md" style="display: flex; flex-direction: column;">
            <div class="bg-gray-50 p-4">
              <h2 class="text-xl font-semibold text-gray-600">Optimization with DPR (Device Pixel Ratio) parameter</h2>
            </div>
            <div class="flex items-center justify-center p-6" style="flex-grow: 1;">
              <img alt="Optimized with DPR" class="h-auto max-w-full" src="{{ Img::url('mountainlake.jpg', ['w' => 800, 'dpr' => 2, 'fm' => 'webp', 'q' => 85]) }}" style="max-width: 800px;">
            </div>
            <div class="bg-gray-50 px-4 py-3" style="margin-top: auto;">
              <p class="text-sm text-gray-600">Image optimized using DPR=2 for retina/4K screens (generates a 1600px wide image)</p>
              <code class="mt-2 block text-xs text-gray-500">
                &lt;img src="@{{ Img::url('mountainlake.jpg', ['w' => 800, 'dpr' => 2, 'fm' => 'webp', 'q' => 85]) }}"
                alt="Optimized with DPR" style="max-width: 800px;"&gt;
              </code>
            </div>
          </div>
        </div>
      </div>

      <div class="mt-12 rounded-lg bg-white p-6 shadow-md">
        <h2 class="mb-4 text-2xl font-bold text-gray-700">Common Use Cases</h2>
        <div class="mb-6 space-y-4 text-gray-600">
          <p>Laravel Glide Enhanced excels in various scenarios:</p>
          <ul class="list-inside list-disc space-y-2 pl-4">
            <li><strong>E-commerce platforms:</strong> Generate product thumbnails, gallery images, and zoomed previews automatically</li>
            <li><strong>Content management systems:</strong> Process user-uploaded images safely and efficiently</li>
            <li><strong>Social media applications:</strong> Create properly sized shared images with the 'social' preset</li>
            <li><strong>Performance optimization:</strong> Serve WebP images to modern browsers while maintaining JPG fallbacks</li>
            <li><strong>Responsive design:</strong> Implement srcset and picture elements with minimal effort</li>
          </ul>
        </div>
        <div class="rounded-lg bg-yellow-50 p-4 text-yellow-700">
          <p class="flex items-center">
            <svg class="mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path clip-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" fill-rule="evenodd" />
            </svg>
            Pro tip: Configure WebP conversion and image presets in your config/images.php file to ensure consistent image quality across your entire application.
          </p>
        </div>
      </div>

      <div class="mt-12 rounded-lg bg-white p-6 shadow-md">
        <h2 class="mb-4 text-2xl font-bold text-gray-700">Other Available Options</h2>
        <p class="mb-6 text-gray-600">Laravel Glide Enhanced provides numerous image manipulation parameters. Here are some of the most commonly used options:</p>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
          <div class="rounded border border-gray-200 p-4">
            <h3 class="font-semibold text-gray-600">Crop</h3>
            <p class="mt-2 text-sm text-gray-600">Crop images automatically or to measure</p>
            <code class="mt-2 block text-xs text-gray-500">['crop' => '300,200']</code>
          </div>
          <div class="rounded border border-gray-200 p-4">
            <h3 class="font-semibold text-gray-600">Rotation</h3>
            <p class="mt-2 text-sm text-gray-600">Rotate the image in degrees</p>
            <code class="mt-2 block text-xs text-gray-500">['rot' => 90]</code>
          </div>
          <div class="rounded border border-gray-200 p-4">
            <h3 class="font-semibold text-gray-600">Contrast</h3>
            <p class="mt-2 text-sm text-gray-600">Adjust the image contrast</p>
            <code class="mt-2 block text-xs text-gray-500">['con' => 20]</code>
          </div>
          <div class="rounded border border-gray-200 p-4">
            <h3 class="font-semibold text-gray-600">Blur</h3>
            <p class="mt-2 text-sm text-gray-600">Apply blur effect</p>
            <code class="mt-2 block text-xs text-gray-500">['blur' => 5]</code>
          </div>
          <div class="rounded border border-gray-200 p-4">
            <h3 class="font-semibold text-gray-600">Watermark</h3>
            <p class="mt-2 text-sm text-gray-600">Add watermark</p>
            <code class="mt-2 block text-xs text-gray-500">['mark' => 'watermark.png']</code>
          </div>
          <div class="rounded border border-gray-200 p-4">
            <h3 class="font-semibold text-gray-600">Quality</h3>
            <p class="mt-2 text-sm text-gray-600">Adjust compression quality</p>
            <code class="mt-2 block text-xs text-gray-500">['q' => 75]</code>
          </div>
          <div class="rounded border border-gray-200 p-4">
            <h3 class="font-semibold text-gray-600">Gamma</h3>
            <p class="mt-2 text-sm text-gray-600">Gamma correction</p>
            <code class="mt-2 block text-xs text-gray-500">['gam' => 1.5]</code>
          </div>
          <div class="rounded border border-gray-200 p-4">
            <h3 class="font-semibold text-gray-600">Sharpness</h3>
            <p class="mt-2 text-sm text-gray-600">Increase image sharpness</p>
            <code class="mt-2 block text-xs text-gray-500">['sharp' => 15]</code>
          </div>
          <div class="rounded border border-gray-200 p-4">
            <h3 class="font-semibold text-gray-600">DPR</h3>
            <p class="mt-2 text-sm text-gray-600">Pixel ratio for high-density screens</p>
            <code class="mt-2 block text-xs text-gray-500">['dpr' => 2]</code>
          </div>
        </div>
      </div>

      <div class="mt-12 rounded-lg bg-white p-6 shadow-md">
        <h2 class="mb-4 text-2xl font-bold text-gray-700">Using with Eloquent Models</h2>
        <p class="mb-4 text-gray-600">The package includes a convenient <code class="bg-gray-100 px-1 py-0.5">HasImages</code> trait that you can add to your Eloquent models:</p>
        <pre class="mb-6 overflow-x-auto rounded bg-gray-100 p-4 text-sm">
use MacCesar\LaravelGlideEnhanced\Traits\HasImages;

class Product extends Model
{
    use HasImages;
    
    // ...
}

// Later in your code:
$product->getImageUrl('main', ['w' => 600]);
$product->getImageWebpUrl('main', 300);
$product->getImagePreset('main', 'thumbnail');</pre>

        <p class="text-gray-600">This approach allows you to associate images with your models and access them with powerful processing capabilities throughout your application.</p>
      </div>

      <div class="mt-12 rounded-lg bg-white p-6 shadow-md">
        <h2 class="mb-4 text-2xl font-bold text-gray-700">Cache Management</h2>
        <p class="mb-4 text-gray-600">Laravel Glide Enhanced automatically caches processed images for better performance. You can manage the cache with the following artisan commands:</p>
        <pre class="mb-4 overflow-x-auto rounded bg-gray-100 p-4 text-sm">
# Clean all cached images
php artisan images:clean-cache

# Clean only images older than 7 days
php artisan images:clean-cache --days=7</pre>
        <p class="text-gray-600">Configure the default cache duration in your <code class="bg-gray-100 px-1 py-0.5">config/images.php</code> file.</p>
      </div>

      <footer class="mt-12 text-center text-sm text-gray-600">
        <p>LaravelGlideEnhanced - A package for advanced image processing in Laravel</p>
        <p class="mt-2">
          <a class="text-gray-600 underline hover:text-gray-800" href="https://github.com/macCesar/laravel-glide-enhanced" rel="noopener noreferrer" target="_blank">GitHub Repository</a> | MacCesar
        </p>
      </footer>
    </div>
  </body>

</html>
