<?php

namespace Tests\Unit\Rules;

use Illuminate\Support\Facades\Validator;
use Tests\BaseTestCase;

use App\Rules\PositiveIntegerRule;
use App\Rules\PositiveSignedBigIntRule;
use App\Rules\PositiveSignedIntRule;
use App\Rules\PositiveSignedMediumIntRule;
use App\Rules\PositiveSignedSmallIntRule;
use App\Rules\PositiveSignedTinyIntRule;
use App\Rules\SignedBigIntRule;
use App\Rules\SignedIntRule;
use App\Rules\SignedMediumIntRule;
use App\Rules\SignedSmallIntRule;
use App\Rules\SignedTinyIntRule;
use App\Rules\UnsignedBigIntRule;
use App\Rules\UnsignedIntRule;
use App\Rules\UnsignedMediumIntRule;
use App\Rules\UnsignedSmallIntRule;
use App\Rules\UnsignedTinyIntRule;

class AllIntegerRuleTest extends BaseTestCase {

  /**
   * Positive Integer only test cases
   */
  public function test_should_pass_validation_for_positive_integer() {
    $test_array = [0, 1, 2];

    foreach ($test_array as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new PositiveIntegerRule]
      );

      $this->assertTrue($validator->passes(), 'Error in $key=' . $key);
    }
  }

  public function test_should_not_pass_validation_for_positive_integer() {
    $test_array = [-1, -2];

    foreach ($test_array as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new PositiveIntegerRule]
      );

      $this->assertFalse($validator->passes(), 'Error in $key=' . $key);
    }
  }

  /**
   * Signed positive integer test cases
   */
  public function test_should_pass_validation_for_positive_signed_tiny_integer() {
    $test_array = [0, 1, 126, 127];

    foreach ($test_array as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new PositiveSignedTinyIntRule]
      );

      $this->assertTrue($validator->passes(), 'Error in $key=' . $key);
    }
  }

  public function test_should_not_pass_validation_for_positive_signed_tiny_integer() {
    $test_array = [-127, -2, -1];

    foreach ($test_array as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new PositiveSignedTinyIntRule]
      );

      $this->assertFalse($validator->passes(), 'Error in $key=' . $key);
    }
  }

  public function test_should_pass_validation_for_positive_signed_small_integer() {
    $test_array = [0, 1, 32766, 32767];

    foreach ($test_array as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new PositiveSignedSmallIntRule]
      );

      $this->assertTrue($validator->passes(), 'Error in $key=' . $key);
    }
  }

  public function test_should_not_pass_validation_for_positive_signed_small_integer() {
    $test_array = [-32767, -2, -1];

    foreach ($test_array as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new PositiveSignedSmallIntRule]
      );

      $this->assertFalse($validator->passes(), 'Error in $key=' . $key);
    }
  }

  public function test_should_pass_validation_for_positive_signed_medium_integer() {
    $test_array = [0, 1, 8388606, 8388607];

    foreach ($test_array as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new PositiveSignedMediumIntRule]
      );

      $this->assertTrue($validator->passes(), 'Error in $key=' . $key);
    }
  }

  public function test_should_not_pass_validation_for_positive_signed_medium_integer() {
    $test_array = [-8388607, -2, -1];

    foreach ($test_array as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new PositiveSignedMediumIntRule]
      );

      $this->assertFalse($validator->passes(), 'Error in $key=' . $key);
    }
  }

  public function test_should_pass_validation_for_positive_signed_integer() {
    $test_array = [0, 1, 2147483646, 2147483647];

    foreach ($test_array as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new PositiveSignedIntRule]
      );

      $this->assertTrue($validator->passes(), 'Error in $key=' . $key);
    }
  }

  public function test_should_not_pass_validation_for_positive_signed_integer() {
    $test_array = [-2147483647, -2, -1];

    foreach ($test_array as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new PositiveSignedIntRule]
      );

      $this->assertFalse($validator->passes(), 'Error in $key=' . $key);
    }
  }

  public function test_should_pass_validation_for_positive_signed_big_integer() {
    $test_array = [0, 1, 9223372036854775806, 9223372036854775807];

    foreach ($test_array as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new PositiveSignedBigIntRule]
      );

      $this->assertTrue($validator->passes(), 'Error in $key=' . $key);
    }
  }

  public function test_should_not_pass_validation_for_positive_signed_big_integer() {
    $test_array = [-9223372036854775807, -2, -1];

    foreach ($test_array as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new PositiveSignedBigIntRule]
      );

      $this->assertFalse($validator->passes(), 'Error in $key=' . $key);
    }
  }

  /**
   * Signed integer test cases
   */
  public function test_should_pass_validation_for_signed_tiny_integer() {
    $test_array = [0, -127, -126, 126, 127];

    foreach ($test_array as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new SignedTinyIntRule]
      );

      $this->assertTrue($validator->passes(), 'Error in $key=' . $key);
    }
  }

  public function test_should_not_pass_validation_for_signed_tiny_integer() {
    $test_array = [-129, -128, 128, 129];

    foreach ($test_array as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new SignedTinyIntRule]
      );

      $this->assertFalse($validator->passes(), 'Error in $key=' . $key);
    }
  }

  public function test_should_pass_validation_for_signed_small_integer() {
    $test_array = [0, -32767, -32766, 32766, 32767];

    foreach ($test_array as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new SignedSmallIntRule]
      );

      $this->assertTrue($validator->passes(), 'Error in $key=' . $key);
    }
  }

  public function test_should_not_pass_validation_for_signed_small_integer() {
    $test_array = [-32769, -32768, 32768, 32769];

    foreach ($test_array as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new SignedSmallIntRule]
      );

      $this->assertFalse($validator->passes(), 'Error in $key=' . $key);
    }
  }

  public function test_should_pass_validation_for_signed_medium_integer() {
    $test_array = [0, -8388607, -8388606, 8388606, 8388607];

    foreach ($test_array as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new SignedMediumIntRule]
      );

      $this->assertTrue($validator->passes(), 'Error in $key=' . $key);
    }
  }

  public function test_should_not_pass_validation_for_signed_medium_integer() {
    $test_array = [-8388609, -8388608, 8388608, 8388609];

    foreach ($test_array as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new SignedMediumIntRule]
      );

      $this->assertFalse($validator->passes(), 'Error in $key=' . $key);
    }
  }

  public function test_should_pass_validation_for_signed_integer() {
    $test_array = [0, -2147483647, -2147483646, 2147483646, 2147483647];

    foreach ($test_array as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new SignedIntRule]
      );

      $this->assertTrue($validator->passes(), 'Error in $key=' . $key);
    }
  }

  public function test_should_not_pass_validation_for_signed_integer() {
    $test_array = [-2147483649, -2147483648, 2147483648, 2147483649];

    foreach ($test_array as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new SignedIntRule]
      );

      $this->assertFalse($validator->passes(), 'Error in $key=' . $key);
    }
  }

  public function test_should_pass_validation_for_signed_big_integer() {
    $test_array = [0, -9223372036854775807, -9223372036854775806, 9223372036854775806, 9223372036854775807];

    foreach ($test_array as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new SignedBigIntRule]
      );

      $this->assertTrue($validator->passes(), 'Error in $key=' . $key);
    }
  }

  public function test_should_not_pass_validation_for_signed_big_integer() {
    // numbers beyond 9223372036854775807 are set to the same number due to overflow
    $test_array_positive = [9223372036854775808, 9223372036854775809];
    $test_array_negative = [-9223372036854775809, -9223372036854775808];

    foreach ($test_array_positive as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new SignedBigIntRule]
      );

      // Should be true instead of false
      $this->assertTrue($validator->passes(), 'Error in $key=' . $key);
      $this->assertEquals(PHP_INT_MAX, $value, 'Error in $key=' . $key);
    }

    foreach ($test_array_negative as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new SignedBigIntRule]
      );

      // Should be true instead of false
      $this->assertTrue($validator->passes(), 'Error in $key=' . $key);
      $this->assertEquals(PHP_INT_MIN, $value, 'Error in $key=' . $key);
    }
  }

  /**
   * Unigned integer test cases
   */
  public function test_should_pass_validation_for_unsigned_tiny_integer() {
    $test_array = [0, 1, 126, 127, 254, 255];

    foreach ($test_array as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new UnsignedTinyIntRule]
      );

      $this->assertTrue($validator->passes(), 'Error in $key=' . $key);
    }
  }

  public function test_should_not_pass_validation_for_unsigned_tiny_integer() {
    $test_array = [-256, -255, -127, -2, -1, 256];

    foreach ($test_array as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new UnsignedTinyIntRule]
      );

      $this->assertFalse($validator->passes(), 'Error in $key=' . $key);
    }
  }

  public function test_should_pass_validation_for_unsigned_small_integer() {
    $test_array = [0, 1, 32766, 32767, 65535];

    foreach ($test_array as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new UnsignedSmallIntRule]
      );

      $this->assertTrue($validator->passes(), 'Error in $key=' . $key);
    }
  }

  public function test_should_not_pass_validation_for_unsigned_small_integer() {
    $test_array = [-65536, -32767, -2, -1, 65536];

    foreach ($test_array as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new UnsignedSmallIntRule]
      );

      $this->assertFalse($validator->passes(), 'Error in $key=' . $key);
    }
  }

  public function test_should_pass_validation_for_unsigned_medium_integer() {
    $test_array = [0, 1, 8388606, 8388607, 16777215];

    foreach ($test_array as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new UnsignedMediumIntRule]
      );

      $this->assertTrue($validator->passes(), 'Error in $key=' . $key);
    }
  }

  public function test_should_not_pass_validation_for_unsigned_medium_integer() {
    $test_array = [-16777216, -8388607, -2, -1, 16777216];

    foreach ($test_array as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new UnsignedMediumIntRule]
      );

      $this->assertFalse($validator->passes(), 'Error in $key=' . $key);
    }
  }

  public function test_should_pass_validation_for_unsigned_integer() {
    $test_array = [0, 1, 2147483646, 2147483647, 4294967295];

    foreach ($test_array as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new UnsignedIntRule]
      );

      $this->assertTrue($validator->passes(), 'Error in $key=' . $key);
    }
  }

  public function test_should_not_pass_validation_for_unsigned_integer() {
    $test_array = [-4294967296, -2147483647, -2, -1, 4294967296];

    foreach ($test_array as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new UnsignedIntRule]
      );

      $this->assertFalse($validator->passes(), 'Error in $key=' . $key);
    }
  }

  public function test_should_pass_validation_for_unsigned_big_integer() {
    $test_array = [0, 1, 9223372036854775806, 9223372036854775807, 18446744073709551615];

    foreach ($test_array as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new UnsignedBigIntRule]
      );

      $this->assertTrue($validator->passes(), 'Error in $key=' . $key);
    }
  }

  public function test_should_not_pass_validation_for_unsigned_big_integer() {
    // numbers beyond 9223372036854775807 are set to the same number due to overflow
    $test_array = [-18446744073709551615, -2, -1];

    foreach ($test_array as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new PositiveSignedBigIntRule]
      );

      $this->assertFalse($validator->passes(), 'Error in $key=' . $key);
    }

    $test_overflow = 18446744073709551616;

    $validator = Validator::make(
      ['test' => $test_overflow],
      ['test' => new UnsignedBigIntRule]
    );

    $this->assertTrue($validator->passes());
  }
}
