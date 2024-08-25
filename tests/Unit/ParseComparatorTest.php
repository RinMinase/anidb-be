<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ParseComparatorTest extends TestCase {

  public function test_should_parse_comparator_symbols() {
    $test_symbol = '>';
    $actual = parse_comparator($test_symbol);
    $expected = '>';
    $this->assertEquals($expected, $actual);

    $test_symbol = '>=';
    $actual = parse_comparator($test_symbol);
    $expected = '>=';
    $this->assertEquals($expected, $actual);

    $test_symbol = '<';
    $actual = parse_comparator($test_symbol);
    $expected = '<';
    $this->assertEquals($expected, $actual);

    $test_symbol = '<=';
    $actual = parse_comparator($test_symbol);
    $expected = '<=';
    $this->assertEquals($expected, $actual);
  }

  public function test_should_parse_comparator_short_text() {
    $test_symbol = 'gt';
    $actual = parse_comparator($test_symbol);
    $expected = '>';
    $this->assertEquals($expected, $actual);

    $test_symbol = 'gte';
    $actual = parse_comparator($test_symbol);
    $expected = '>=';
    $this->assertEquals($expected, $actual);

    $test_symbol = 'lt';
    $actual = parse_comparator($test_symbol);
    $expected = '<';
    $this->assertEquals($expected, $actual);

    $test_symbol = 'lte';
    $actual = parse_comparator($test_symbol);
    $expected = '<=';
    $this->assertEquals($expected, $actual);
  }

  public function test_should_parse_comparator_words() {
    $test_symbol = 'greater than';
    $actual = parse_comparator($test_symbol);
    $expected = '>';
    $this->assertEquals($expected, $actual);

    $test_symbol = 'greater than equal';
    $actual = parse_comparator($test_symbol);
    $expected = '>=';
    $this->assertEquals($expected, $actual);

    $test_symbol = 'greater than or equal';
    $actual = parse_comparator($test_symbol);
    $expected = '>=';
    $this->assertEquals($expected, $actual);

    $test_symbol = 'less than';
    $actual = parse_comparator($test_symbol);
    $expected = '<';
    $this->assertEquals($expected, $actual);

    $test_symbol = 'less than equal';
    $actual = parse_comparator($test_symbol);
    $expected = '<=';
    $this->assertEquals($expected, $actual);

    $test_symbol = 'less than or equal';
    $actual = parse_comparator($test_symbol);
    $expected = '<=';
    $this->assertEquals($expected, $actual);
  }

  public function test_should_return_blank_string_on_invalid_input() {
    $test_symbol = 'invalid string';
    $actual = parse_comparator($test_symbol);
    $expected = '';
    $this->assertEquals($expected, $actual);

    $test_symbol = ' ';
    $actual = parse_comparator($test_symbol);
    $expected = '';
    $this->assertEquals($expected, $actual);

    $test_symbol = '';
    $actual = parse_comparator($test_symbol);
    $expected = '';
    $this->assertEquals($expected, $actual);
  }
}
