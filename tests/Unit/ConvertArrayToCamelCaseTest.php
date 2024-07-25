<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ConvertArrayToCamelCaseTest extends TestCase {
  public function test_should_return_camelcase_from_snakecase_array_successfully() {
    $test_array = [
      'snake_case' => 'value',
      'camelCase' => 'value',
      'kebab-case' => 'value',
      'PascalCase' => 'value',
    ];

    $actual = convert_array_to_camel_case($test_array);

    $expected = [
      'snakeCase' => 'value', // this should be changed
      'pascalCase' => 'value', // this should be changed
      'camelCase' => 'value',
      'kebab-case' => 'value',
    ];

    $this->assertEquals($expected, $actual);
  }

  public function test_should_return_blank_array_on_blank_array_input() {
    $actual = convert_array_to_camel_case([]);
    $expected = [];

    $this->assertEquals($expected, $actual);
    $this->assertCount(count($expected), $actual);
  }
}
