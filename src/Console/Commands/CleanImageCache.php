<?php

namespace MacCesar\LaravelGlideEnhanced\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanImageCache extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'images:clean-cache {--days=0}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Cleans cached images older than the specified number of days';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    // Use the parameter value or the configuration value or 30 days as default
    $days = $this->option('days') ?: config('images.cache_days', 30);

    $this->info("Cleaning image cache older than {$days} days...");

    if ($days > 0) {
      // Get cache directory
      $cacheDir = config('images.cache.path', 'cache/glide');
      $deleted = 0;

      // We are filtering files by modification date
      if (Storage::exists($cacheDir)) {
        $files = Storage::allFiles($cacheDir);
        $now = Carbon::now();

        foreach ($files as $file) {
          // Get the modification time
          $lastModified = Carbon::createFromTimestamp(Storage::lastModified($file));
          $daysOld = $lastModified->diffInDays($now);

          if ($daysOld >= $days) {
            Storage::delete($file);
            $deleted++;
          }
        }

        $this->info("Deleted {$deleted} cached images older than {$days} days.");
      } else {
        $this->info("Cache directory not found.");
      }
    } else {
      // Clean all cache
      Storage::deleteDirectory(config('images.cache.path', 'cache/glide'));
      $this->info("All image cache has been cleaned.");
    }

    return 0;
  }
}
