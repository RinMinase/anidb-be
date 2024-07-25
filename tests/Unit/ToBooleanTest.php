<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ToBooleanTest extends TestCase {
  public function test_should_convert_inputs_to_boolean_successfully() {
    $test_data_true = [
      'true',
      'TRUE',
      'True',
      '1',
      1,
    ];

    $test_data_false = [
      'false',
      'FALSE',
      'False',
      '0',
      0,
    ];

    foreach ($test_data_true as $value) {
      $actual = to_boolean($value);
      $this->assertTrue($actual);
    }

    foreach ($test_data_false as $value) {
      $actual = to_boolean($value);
      $this->assertFalse($actual);
    }
  }

  public function test_should_fail_in_converting_invalid_boolean_values() {
    $test_data = [
      'any value',
      2,
      -1,
    ];

    foreach ($test_data as $value) {
      $actual = to_boolean($value);
      $this->assertNull($actual);
    }
  }
}
