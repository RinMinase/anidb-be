<?php

namespace App\Commands;

use Exception;
use Illuminate\Console\Command;

class GenerateApiKey extends Command {

	protected $signature = "api:generate {length?}";
	protected $description = "Generate a new API Key";

	public function handle() {
		$length = ($this->argument('length')) ? $this->argument('length') : 32;
		$envFile = base_path('.env');

		try {
			if (file_exists($envFile)) {
				$apiKey = random_string($length);

				$this->info('Client API Key:');
				$this->info($apiKey);

				file_put_contents(
					$envFile,
					preg_replace('/API_KEY=\w*/', 'API_KEY=' . $apiKey, file_get_contents($envFile))
				);

				$this->info('');
				$this->info('Placed on ENV File successfully');
			} else {
				$this->error('ENV File not present');
			}
		} catch (Exception $e) {
			$this->error('An error occurred');
		}
	}

}
