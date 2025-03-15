<?php

namespace Tests\Unit\Rules;

use Illuminate\Support\Facades\Validator;
use Tests\BaseTestCase;

use App\Rules\YearRule;

class YearRuleTest extends BaseTestCase {
  public function test_should_pass_validation() {
    $test_array = [
      1900,
      1901,
      2998,
      2999,
      '1900',
      '1901',
      '2998',
      '2999',
    ];

    foreach ($test_array as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new YearRule]
      );

      $this->assertTrue($validator->passes(), 'Error in $key=' . $key);
    }
  }

  public function test_should_not_pass_validation() {
    $test_array = [
      1800,
      1898,
      1899,
      3000,
      3001,
      '1800',
      '1898',
      '1899',
      '3000',
      '3001',
      'string',
      'null',
      null,
      0,
      '0',
      -1,
      '-1',
      -1900,
      '-1900',
      -2999,
      '-2999',
    ];

    foreach ($test_array as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new YearRule]
      );

      $this->assertFalse($validator->passes(), 'Error in $key=' . $key);
    }
  }
}
