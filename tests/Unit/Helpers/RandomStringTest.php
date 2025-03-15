<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\TestCase;

class RandomStringTest extends TestCase {

  public function test_should_create_a_random_string_of_provided_length_successfully() {
    $test_length = 5;
    $actual = rand_str($test_length);
    $this->assertEquals($test_length, strlen($actual));

    $test_length = 64;
    $actual = rand_str($test_length);
    $this->assertEquals($test_length, strlen($actual));

    $test_length = 128;
    $actual = rand_str($test_length);
    $this->assertEquals($test_length, strlen($actual));

    $test_length = 256;
    $actual = rand_str($test_length);
    $this->assertEquals($test_length, strlen($actual));

    $test_length = 512;
    $actual = rand_str($test_length);
    $this->assertEquals($test_length, strlen($actual));
  }

  public function test_should_create_a_random_string_of_only_characters_when_alpha_only_is_true() {
    $test_length = 1;

    for ($i = 0; $i < 30; $i++) {
      $actual = rand_str($test_length, true);

      $this->assertEquals($test_length, strlen($actual));
      $this->assertTrue(ctype_lower($actual));
    }
  }

  public function test_should_create_a_random_string_of_alphanumeric_characters_when_alpha_only_is_false() {
    $test_length = 1;

    for ($i = 0; $i < 30; $i++) {
      $actual = rand_str($test_length, false);

      $this->assertEquals($test_length, strlen($actual));
      $this->assertTrue(ctype_alnum($actual));
    }
  }

  public function test_should_create_a_random_string_of_length_20_when_no_args_provided() {
    $expected_length = 20;
    $actual = rand_str();
    $this->assertEquals($expected_length, strlen($actual));
  }
}
