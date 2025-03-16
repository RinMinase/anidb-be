<?php

namespace App\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateApiKey extends Command {

  protected $signature = 'app:generate-api-key';
  protected $description = 'Generate an API key and store it in .env';

  public function handle() {
    $rand_key = Str::random(36);
    $path = base_path('.env');

    if (file_exists($path)) {
      file_put_contents(
        $path,
        str_replace(
          'API_KEY' . '=' . env('API_KEY'),
          'API_KEY' . '=' . $rand_key,
          file_get_contents($path)
        )
      );
    }

    $this->info('API Key Generated: ' . $rand_key);
  }
}
