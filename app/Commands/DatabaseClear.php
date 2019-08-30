<?php

namespace App\Commands;

use Exception;
use Illuminate\Console\Command;

class DatabaseClear extends Command {

	protected $signature = "db:clear {database}";
	protected $description = "Clear database contents :: 'db:clear <database>'";

	public function handle() {
		$database = $this->argument('database');

		try {
			$this->info($database);
		} catch (Exception $e) {
			$this->error('An error occurred');
		}
	}

}
