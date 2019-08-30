<?php

namespace App\Commands;

use App\Commands\KernelApplication as Artisan;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {

	protected $commands = [
		DatabaseClear::class,
		GenerateApiKey::class,
	];

	protected function getArtisan() {
		if (is_null($this->artisan)) {
			$artisan = new Artisan($this->app, $this->app->make('events'), $this->app->version());
			$this->artisan = $artisan->resolveCommands($this->commands);
		}

		return $this->artisan;
	}

}
