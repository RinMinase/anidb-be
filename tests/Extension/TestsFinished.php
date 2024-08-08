<?php

namespace Tests\Extension;

use Illuminate\Support\Facades\Artisan;
use PHPUnit\Event\Application\Finished;
use PHPUnit\Event\Application\FinishedSubscriber;

class TestsFinished implements FinishedSubscriber {
  public function notify(Finished $event): void {
    // print PHP_EOL . 'Running termination scripts' . PHP_EOL;
  }
}
