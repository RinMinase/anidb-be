<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase as LaravelBaseTestCase;

trait CreateApplication {
  public function createApplication() {
    $app = require __DIR__ . '/../bootstrap/app.php';
    $app->make(Kernel::class)->bootstrap();

    return $app;
  }
}

abstract class BaseTestCase extends LaravelBaseTestCase {
  use CreateApplication;
}
