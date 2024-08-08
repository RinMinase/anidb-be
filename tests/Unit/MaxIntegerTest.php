<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

use App\Enums\IntegerSizesEnum;
use App\Enums\IntegerTypesEnum;

class MaxIntegerTest extends TestCase {
  public function test_should_return_max_unsigned_integer() {
    $expected = [
      'tiny' => 255,
      'small' => 65535,
      'medium' => 16777215,
      'default' => 4294967295,
      'big' => 18446744073709551615,
    ];

    $actual = max_int(IntegerTypesEnum::UNSIGNED, IntegerSizesEnum::TINY);
    $this->assertEquals($expected['tiny'], $actual);

    $actual = max_int(IntegerTypesEnum::UNSIGNED, IntegerSizesEnum::SMALL);
    $this->assertEquals($expected['small'], $actual);

    $actual = max_int(IntegerTypesEnum::UNSIGNED, IntegerSizesEnum::MEDIUM);
    $this->assertEquals($expected['medium'], $actual);

    $actual = max_int(IntegerTypesEnum::UNSIGNED, IntegerSizesEnum::DEFAULT);
    $this->assertEquals($expected['default'], $actual);

    $actual = max_int(IntegerTypesEnum::UNSIGNED, IntegerSizesEnum::BIG);
    $this->assertEquals($expected['big'], $actual);
  }

  public function test_should_return_max_signed_positive_integer() {
    $expected = [
      'tiny' => 127,
      'small' => 32767,
      'medium' => 8388607,
      'default' => 2147483647,
      'big' => 9223372036854775807,
    ];

    $actual = max_int(IntegerTypesEnum::SIGNED, IntegerSizesEnum::TINY);
    $this->assertEquals($expected['tiny'], $actual);

    $actual = max_int(IntegerTypesEnum::SIGNED, IntegerSizesEnum::SMALL);
    $this->assertEquals($expected['small'], $actual);

    $actual = max_int(IntegerTypesEnum::SIGNED, IntegerSizesEnum::MEDIUM);
    $this->assertEquals($expected['medium'], $actual);

    $actual = max_int(IntegerTypesEnum::SIGNED, IntegerSizesEnum::DEFAULT);
    $this->assertEquals($expected['default'], $actual);

    $actual = max_int(IntegerTypesEnum::SIGNED, IntegerSizesEnum::BIG);
    $this->assertEquals($expected['big'], $actual);
  }

  public function test_should_return_max_signed_negative_integer() {
    $test_is_negative = true;

    $expected = [
      'tiny' => -127,
      'small' => -32767,
      'medium' => -8388607,
      'default' => -2147483647,
      'big' => -9223372036854775807,
    ];

    $actual = max_int(IntegerTypesEnum::SIGNED, IntegerSizesEnum::TINY, $test_is_negative);
    $this->assertEquals($expected['tiny'], $actual);

    $actual = max_int(IntegerTypesEnum::SIGNED, IntegerSizesEnum::SMALL, $test_is_negative);
    $this->assertEquals($expected['small'], $actual);

    $actual = max_int(IntegerTypesEnum::SIGNED, IntegerSizesEnum::MEDIUM, $test_is_negative);
    $this->assertEquals($expected['medium'], $actual);

    $actual = max_int(IntegerTypesEnum::SIGNED, IntegerSizesEnum::DEFAULT, $test_is_negative);
    $this->assertEquals($expected['default'], $actual);

    $actual = max_int(IntegerTypesEnum::SIGNED, IntegerSizesEnum::BIG, $test_is_negative);
    $this->assertEquals($expected['big'], $actual);
  }

  public function test_should_return_max_signed_positive_integer_on_no_params() {
    $expected = 2147483647;
    $actual = max_int();
    $this->assertEquals($expected, $actual);
  }
}
