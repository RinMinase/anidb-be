<?php

/* Create The Application */

$app = new Illuminate\Foundation\Application(
  $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

/* Bind Important Interfaces */

$app->singleton(
  Illuminate\Contracts\Http\Kernel::class,
  App\Console\HttpKernel::class
);

$app->singleton(
  Illuminate\Contracts\Console\Kernel::class,
  App\Console\ConsoleKernel::class
);

$app->singleton(
  Illuminate\Contracts\Debug\ExceptionHandler::class,
  App\Exceptions\Handler::class
);

/* Return The Application */

return $app;
