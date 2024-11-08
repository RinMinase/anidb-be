<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class IsUUIDTest extends TestCase {
  public function test_should_return_true_on_valid_uuid_format() {
    $test_uuids = [
      'ef3392ad-999e-43d0-a3ac-fa5649c46f26',

      // not a valid uuid, but a valid uuid format
      // {8 hex}-{4 hex}-[4]{3 hex}-[89ab]{3 hex}-{12 hex}
      'aaaaaaaa-1234-4bbb-8aaa-bbbbbbbb1234',
    ];

    foreach ($test_uuids as $key => $value) {
      $actual = is_uuid($value);
      $this->assertTrue($actual, 'Error in $key=' . $key);
    }
  }

  public function test_should_return_false_on_invalid_uuid_format() {
    $test_uuids = [
      'aaaaaaaa-1234-1234-1234-bbbbbbbb1234',
      'invalid uuid',
      'invalid',
      1,
      0,
      -1,
      '1',
      'a',
      '0',
      true,
      false,
      "true",
      "false",
    ];

    foreach ($test_uuids as $key => $value) {
      $actual = is_uuid($value);
      $this->assertFalse($actual, 'Error in $key=' . $key);
    }
  }
}
