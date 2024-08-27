<?php

namespace Tests\Unit;

use Error;
use Tests\BaseUnitTestCase;

class ParseSeasonTest extends BaseUnitTestCase {

  public function test_should_parse_season_and_return_correct_array() {
    $expected = [2020, 'winter'];
    $test_strings = ['winter 2020', 'Winter 2020', 'WINTER 2020', '2020 winter'];
    foreach ($test_strings as $test_string) {
      $actual = parse_season($test_string);
      $this->assertEquals($expected, $actual);
    }

    $expected = [2020, 'spring'];
    $test_strings = ['spring 2020', 'Spring 2020', 'SPRING 2020', '2020 spring'];
    foreach ($test_strings as $test_string) {
      $actual = parse_season($test_string);
      $this->assertEquals($expected, $actual);
    }

    $expected = [2020, 'summer'];
    $test_strings = ['summer 2020', 'Summer 2020', 'SUMMER 2020', '2020 summer'];
    foreach ($test_strings as $test_string) {
      $actual = parse_season($test_string);
      $this->assertEquals($expected, $actual);
    }

    $expected = [2020, 'fall'];
    $test_strings = ['fall 2020', 'Fall 2020', 'FALL 2020', '2020 fall'];
    foreach ($test_strings as $test_string) {
      $actual = parse_season($test_string);
      $this->assertEquals($expected, $actual);
    }

    $test_string = '2020';
    $actual = parse_season($test_string);
    $expected = [2020];
    $this->assertEquals($expected, $actual);

    $test_string = '1900';
    $actual = parse_season($test_string);
    $expected = [1900];
    $this->assertEquals($expected, $actual);

    $test_string = '2999';
    $actual = parse_season($test_string);
    $expected = [2999];
    $this->assertEquals($expected, $actual);

    $test_string = 'winter 2020';
    $actual = parse_season($test_string);
    $expected = [2020, 'winter'];
    $this->assertEquals($expected, $actual);

    $test_string = 'winter 1900';
    $actual = parse_season($test_string);
    $expected = [1900, 'winter'];
    $this->assertEquals($expected, $actual);

    $test_string = 'winter 2000';
    $actual = parse_season($test_string);
    $expected = [2000, 'winter'];
    $this->assertEquals($expected, $actual);

    $test_string = 'winter 2100';
    $actual = parse_season($test_string);
    $expected = [2100, 'winter'];
    $this->assertEquals($expected, $actual);

    $test_string = 'winter 2999';
    $actual = parse_season($test_string);
    $expected = [2999, 'winter'];
    $this->assertEquals($expected, $actual);
  }

  public function test_should_throw_error_on_invalid_input() {
    $test_string = 'invalid string';
    $this->assertException(Error::class, fn() => parse_season($test_string));

    $test_string = 'invalid 2020';
    $this->assertException(Error::class, fn() => parse_season($test_string));

    $test_string = '2021 invalid';
    $this->assertException(Error::class, fn() => parse_season($test_string));

    $test_string = 'invalid';
    $this->assertException(Error::class, fn() => parse_season($test_string));

    $test_string = '3000 invalid';
    $this->assertException(Error::class, fn() => parse_season($test_string));

    $test_string = '3000';
    $this->assertException(Error::class, fn() => parse_season($test_string));

    $test_string = '1899 invalid';
    $this->assertException(Error::class, fn() => parse_season($test_string));

    $test_string = '1899';
    $this->assertException(Error::class, fn() => parse_season($test_string));
  }
}
