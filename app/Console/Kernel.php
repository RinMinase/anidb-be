<?php

namespace App\Console;

use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {

	protected $commands = [
		Commands\GenerateApiKey::class,
	];

}
