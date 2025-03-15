<?php

namespace Tests\Unit\Rules;

use Illuminate\Support\Facades\Validator;
use Tests\BaseTestCase;

use App\Rules\JsonRule;

class JsonRuleTest extends BaseTestCase {
  public function test_should_pass_validation() {
    $test_array = [
      '{"test": 1}',
      '{"test": 1, "test2": "string"}',
      '{"test": 1, "test2": "string", "object": {"key": "value"}}',
      '{"test": 1, "test2": "string", "object": {"key": "value"}, "array": [1,2,3]}',
      '{"test": 1, "test2": "string", "object": {"key": "value"}, "array": ["value1","value2"]}',
      '[{"key": "value"}, {"key": "value"}, {"key": "value"}]',
    ];

    foreach ($test_array as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new JsonRule]
      );

      $this->assertTrue($validator->passes(), 'Error in $key=' . $key);
    }
  }

  public function test_should_not_pass_validation() {
    $test_array = [
      '"test": 1',
      '{test: 1}',
      '{"test": string}',
      '{"test": { key: 1 }}',
      '{"test": { "key": value }}',
      '{"test": [ value ]',
      '{"test": [ { "key": "value" } ]',
      '{[{"key": "value"}, {"key": "value"}, {"key": "value"}]}',
      '{ array: [{"key": "value"}, {"key": "value"}, {"key": "value"}]}',
      '{ "array": {{"key": "value"}, {"key": "value"}, {"key": "value"}}}',
    ];

    foreach ($test_array as $key => $value) {
      $validator = Validator::make(
        ['test' => $value],
        ['test' => new JsonRule]
      );

      $this->assertFalse($validator->passes(), 'Error in $key=' . $key);
    }
  }
}
