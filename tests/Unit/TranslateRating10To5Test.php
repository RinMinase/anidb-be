<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class TranslateRating10To5Test extends TestCase {
  public function test_should_return_translated_rating() {
    $actual = translate_rating_10_to_5('10');
    $this->assertEquals(5, $actual);

    $actual = translate_rating_10_to_5(10);
    $this->assertEquals(5, $actual);

    $actual = translate_rating_10_to_5('9');
    $this->assertEquals(5, $actual);

    $actual = translate_rating_10_to_5(9);
    $this->assertEquals(5, $actual);

    $actual = translate_rating_10_to_5('8');
    $this->assertEquals(4, $actual);

    $actual = translate_rating_10_to_5(8);
    $this->assertEquals(4, $actual);

    $actual = translate_rating_10_to_5('7');
    $this->assertEquals(3, $actual);

    $actual = translate_rating_10_to_5(7);
    $this->assertEquals(3, $actual);

    $actual = translate_rating_10_to_5('6');
    $this->assertEquals(2, $actual);

    $actual = translate_rating_10_to_5(6);
    $this->assertEquals(2, $actual);

    $actual = translate_rating_10_to_5('5');
    $this->assertEquals(2, $actual);

    $actual = translate_rating_10_to_5(5);
    $this->assertEquals(2, $actual);

    $actual = translate_rating_10_to_5('4');
    $this->assertEquals(1, $actual);

    $actual = translate_rating_10_to_5(4);
    $this->assertEquals(1, $actual);

    $actual = translate_rating_10_to_5('3');
    $this->assertEquals(1, $actual);

    $actual = translate_rating_10_to_5(3);
    $this->assertEquals(1, $actual);

    $actual = translate_rating_10_to_5('2');
    $this->assertEquals(1, $actual);

    $actual = translate_rating_10_to_5(2);
    $this->assertEquals(1, $actual);

    $actual = translate_rating_10_to_5('1');
    $this->assertEquals(1, $actual);

    $actual = translate_rating_10_to_5(1);
    $this->assertEquals(1, $actual);

    $actual = translate_rating_10_to_5('0');
    $this->assertEquals(0, $actual);

    $actual = translate_rating_10_to_5(0);
    $this->assertEquals(0, $actual);
  }

  public function test_should_return_null_on_blank_or_invalid_rating() {
    $test_data = [
      '',
      'True',
      null,
      '11',
      11,
      '-1',
      -1,
    ];

    foreach ($test_data as $value) {
      $actual = translate_rating_10_to_5($value, true);
      $this->assertNull($actual, 'Error in $value=' . $value);
    }
  }

  public function test_should_return_0_on_blank_or_invalid_rating() {
    $test_data = [
      '',
      'True',
      null,
      '11',
      11,
      '-1',
      -1,
    ];

    foreach ($test_data as $value) {
      $actual = translate_rating_10_to_5($value, false);
      $this->assertEquals(0, $actual, 'Error in $value=' . $value);
    }

    foreach ($test_data as $value) {
      $actual = translate_rating_10_to_5($value);
      $this->assertEquals(0, $actual, 'Error in $value=' . $value);
    }
  }
}
