<?php

namespace MacCesar\LaravelGlideEnhanced\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanImageCache extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'images:clean-cache {--days= : Delete files at least this many days old; 0 deletes all files}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Cleans cached images older than the specified number of days';

  /**
   * Execute the console command.
   */
  public function handle(): int
  {
    $option = $this->option('days');
    $days = $option === null ? config('images.cache.lifetime', 30) : $option;

    if (filter_var($days, FILTER_VALIDATE_INT) === false || (int) $days < 0) {
      $this->error('The --days option must be a non-negative integer.');

      return self::FAILURE;
    }

    $days = (int) $days;
    $disk = config('images.cache.disk', 'local');
    $storage = Storage::disk($disk);
    $cacheDir = config('images.cache.path', 'cache/glide');

    $this->info("Cleaning image cache older than {$days} days...");

    if ($days > 0) {
      $deleted = 0;

      // We are filtering files by modification date
      if ($storage->exists($cacheDir)) {
        $files = $storage->allFiles($cacheDir);
        $cutoff = now()->subDays($days)->getTimestamp();

        foreach ($files as $file) {
          if ($storage->lastModified($file) <= $cutoff) {
            $storage->delete($file);
            $deleted++;
          }
        }

        $this->info("Deleted {$deleted} cached images older than {$days} days.");
      } else {
        $this->info("Cache directory not found.");
      }
    } else {
      // Clean all cache
      $storage->deleteDirectory($cacheDir);
      $this->info("All image cache has been cleaned.");
    }

    return self::SUCCESS;
  }
}
