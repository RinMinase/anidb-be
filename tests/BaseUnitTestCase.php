<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

abstract class BaseUnitTestCase extends TestCase {

  /**
   * Asserts that the given callback throws the given exception.
   *
   * @param string $expectClass The name of the expected exception class
   * @param callable $callback A callback which should throw the exception
   */
  protected function assertException(string $expectClass, callable $callback) {
    // https://stackoverflow.com/a/46972615
    try {
      $callback();
    } catch (\Throwable $exception) {
      $this->assertInstanceOf($expectClass, $exception, 'An invalid exception was thrown');
      return;
    }

    $this->fail('No exception was thrown');
  }
}
