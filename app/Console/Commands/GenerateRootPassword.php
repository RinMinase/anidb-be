<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateRootPassword extends Command {
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:generate-root-password';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Command description';

  /**
   * Execute the console command.
   */
  public function handle() {
    $rand_key = Str::random(36);
    $path = base_path('.env');

    if (file_exists($path)) {
      file_put_contents(
        $path,
        str_replace(
          'APP_REGISTRATION_ROOT_PASSWORD' . '=' . env('APP_REGISTRATION_ROOT_PASSWORD'),
          'APP_REGISTRATION_ROOT_PASSWORD' . '=' . $rand_key,
          file_get_contents($path)
        )
      );
    }

    $this->info('Key Generated: ' . $rand_key);
  }
}
