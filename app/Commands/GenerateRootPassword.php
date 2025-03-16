<?php

namespace App\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateRootPassword extends Command {
  protected $signature = 'app:generate-root-password';
  protected $description = 'Generate a Root Password for admin registration and store it in .env';

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

    $this->info('Root Key Generated: ' . $rand_key);
  }
}
