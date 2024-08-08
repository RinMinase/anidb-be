<?php

namespace Tests;

use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;

// use Tests\Extension\TestsFinished;
// use Tests\Extension\TestsStarted;

class BaseExtension implements Extension {
  public function bootstrap(
    Configuration $configuration,
    Facade $facade,
    ParameterCollection $parameters,
  ): void {

    $facade->registerSubscribers(
      // new TestsStarted(),
      // new TestsFinished(),
    );
  }
}
