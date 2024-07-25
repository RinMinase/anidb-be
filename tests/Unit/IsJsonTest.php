<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class IsJsonTest extends TestCase {
  public function test_should_return_true_on_valid_json() {
    $test_json = '{"number": 1, "string": "value"}';

    $actual = is_json($test_json);

    $this->assertTrue($actual);
  }

  public function test_should_return_false_on_invalid_json() {
    $test_json = '{number: 1, "string": "value"}';
    $actual = is_json($test_json);
    $this->assertFalse($actual);

    $test_json = '{"number": 1, "string": "value"';
    $actual = is_json($test_json);
    $this->assertFalse($actual);

    $test_json = '{"number": 1, "string": value}';
    $actual = is_json($test_json);
    $this->assertFalse($actual);
  }
}
