<?php

namespace App\Commands;

use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {

	protected $commands = [
		GenerateApiKey::class,
	];

}
