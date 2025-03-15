<?php

namespace Tests\Unit\Rules;

use Illuminate\Support\Facades\Validator;
use Tests\BaseTestCase;

use App\Rules\DivisibleBy15Rule;

class DivisibleBy15RuleTest extends BaseTestCase {
  public function test_should_pass_validation() {
    $test_array = [
      0,
      15,
      -15,
      30,
      -30,
    ];

    foreach ($test_array as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new DivisibleBy15Rule]
      );

      $this->assertTrue($validator->passes(), 'Error in $key=' . $key);
    }
  }

  public function test_should_not_pass_validation() {
    $test_array = [
      1,
      -1,
      14,
      -14,
      16,
      -16,
      31,
      -31,
    ];

    foreach ($test_array as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new DivisibleBy15Rule]
      );

      $this->assertFalse($validator->passes(), 'Error in $key=' . $key);
    }
  }
}
