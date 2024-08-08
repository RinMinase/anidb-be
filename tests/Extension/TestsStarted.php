<?php

namespace Tests\Extension;

use PHPUnit\Event\Application\Started;
use PHPUnit\Event\Application\StartedSubscriber;

class TestsStarted implements StartedSubscriber {
  public function notify(Started $event): void {
    // print PHP_EOL . 'Running boostrapping script' . PHP_EOL;
  }
}
