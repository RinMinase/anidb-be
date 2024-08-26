<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class GetComparatorTest extends TestCase {

  public function test_should_return_comparator_if_text_contains_comparators() {
    $test_text = '> some value';
    $actual = get_comparator($test_text);
    $expected = '>';
    $this->assertEquals($expected, $actual);

    $test_text = '>= some value';
    $actual = get_comparator($test_text);
    $expected = '>=';
    $this->assertEquals($expected, $actual);

    $test_text = '< some value';
    $actual = get_comparator($test_text);
    $expected = '<';
    $this->assertEquals($expected, $actual);

    $test_text = '<= some value';
    $actual = get_comparator($test_text);
    $expected = '<=';
    $this->assertEquals($expected, $actual);

    $test_text = 'gt some value';
    $actual = get_comparator($test_text);
    $expected = 'gt';
    $this->assertEquals($expected, $actual);

    $test_text = 'gte some value';
    $actual = get_comparator($test_text);
    $expected = 'gte';
    $this->assertEquals($expected, $actual);

    $test_text = 'lt some value';
    $actual = get_comparator($test_text);
    $expected = 'lt';
    $this->assertEquals($expected, $actual);

    $test_text = 'lte some value';
    $actual = get_comparator($test_text);
    $expected = 'lte';
    $this->assertEquals($expected, $actual);

    $test_text = 'GT some value';
    $actual = get_comparator($test_text);
    $expected = 'gt';
    $this->assertEquals($expected, $actual);

    $test_text = 'GTE some value';
    $actual = get_comparator($test_text);
    $expected = 'gte';
    $this->assertEquals($expected, $actual);

    $test_text = 'LT some value';
    $actual = get_comparator($test_text);
    $expected = 'lt';
    $this->assertEquals($expected, $actual);

    $test_text = 'LTE some value';
    $actual = get_comparator($test_text);
    $expected = 'lte';
    $this->assertEquals($expected, $actual);

    $test_text = 'greater than some value';
    $actual = get_comparator($test_text);
    $expected = 'greater than';
    $this->assertEquals($expected, $actual);

    $test_text = 'greater than equal some value';
    $actual = get_comparator($test_text);
    $expected = 'greater than equal';
    $this->assertEquals($expected, $actual);

    $test_text = 'greater than or equal some value';
    $actual = get_comparator($test_text);
    $expected = 'greater than or equal';
    $this->assertEquals($expected, $actual);

    $test_text = 'less than some value';
    $actual = get_comparator($test_text);
    $expected = 'less than';
    $this->assertEquals($expected, $actual);

    $test_text = 'less than equal some value';
    $actual = get_comparator($test_text);
    $expected = 'less than equal';
    $this->assertEquals($expected, $actual);

    $test_text = 'less than or equal some value';
    $actual = get_comparator($test_text);
    $expected = 'less than or equal';
    $this->assertEquals($expected, $actual);
  }

  public function test_should_return_null_if_text_has_no_comparators() {
    $test_text = 'string';
    $actual = get_comparator($test_text);
    $this->assertNull($actual);

    $test_text = 'string value';
    $actual = get_comparator($test_text);
    $this->assertNull($actual);

    $test_text = '<> value';
    $actual = get_comparator($test_text);
    $this->assertNull($actual);

    $test_text = '>< value';
    $actual = get_comparator($test_text);
    $this->assertNull($actual);

    $test_text = 'is greater than value';
    $actual = get_comparator($test_text);
    $this->assertNull($actual);

    $test_text = 'less thanvalue';
    $actual = get_comparator($test_text);
    $this->assertNull($actual);

    $test_text = '<value';
    $actual = get_comparator($test_text);
    $this->assertNull($actual);
  }
}
