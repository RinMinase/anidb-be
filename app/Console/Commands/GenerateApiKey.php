<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;

class GenerateApiKey extends Command {

	protected $signature = "generate:api";
	protected $description = "Generate a new API Key";

	public function handle() {
		try {
			$this->info($this->generateRandomString());
		} catch (Exception $e) {
			$this->error("An error occurred");
		}
	}

	private function generateRandomString($length = 32) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';

		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}

		return $randomString;
	}
}
