<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\TestCase;

class ParseFilesizeTest extends TestCase {

  public function test_should_parse_byte_filesize_and_return_filesize_string() {
    $test_filesize = 1_864;
    $actual = parse_filesize($test_filesize);
    $expected = '1.82 KB';
    $this->assertEquals($expected, $actual);

    $test_filesize = 1_321_206;
    $actual = parse_filesize($test_filesize);
    $expected = '1.26 MB';
    $this->assertEquals($expected, $actual);

    $test_filesize = 1_320_702_444;
    $actual = parse_filesize($test_filesize);
    $expected = '1.23 GB';
    $this->assertEquals($expected, $actual);

    $test_filesize = 1_693_247_906_776;
    $actual = parse_filesize($test_filesize);
    $expected = '1.54 TB';
    $this->assertEquals($expected, $actual);
  }

  public function test_should_parse_filesize_with_forced_unit_in_kb() {
    $test_forced_unit = 'KB';

    $test_filesize = 1_864;
    $actual = parse_filesize($test_filesize, $test_forced_unit);
    $expected = '1.82 KB';
    $this->assertEquals($expected, $actual);

    $test_filesize = 1_321_206;
    $actual = parse_filesize($test_filesize, $test_forced_unit);
    $expected = '1290.24 KB';
    $this->assertEquals($expected, $actual);

    $test_filesize = 1_320_702_444;
    $actual = parse_filesize($test_filesize, $test_forced_unit);
    $expected = '1289748.48 KB';
    $this->assertEquals($expected, $actual);

    $test_filesize = 1_693_247_906_776;
    $actual = parse_filesize($test_filesize, $test_forced_unit);
    $expected = '1653562408.96 KB';
    $this->assertEquals($expected, $actual);
  }

  public function test_should_parse_filesize_with_forced_unit_in_mb() {
    $test_forced_unit = 'MB';

    $test_filesize = 1_864;
    $actual = parse_filesize($test_filesize, $test_forced_unit);
    $expected = '0 MB';
    $this->assertEquals($expected, $actual);

    $test_filesize = 1_321_206;
    $actual = parse_filesize($test_filesize, $test_forced_unit);
    $expected = '1.26 MB';
    $this->assertEquals($expected, $actual);

    $test_filesize = 1_320_702_444;
    $actual = parse_filesize($test_filesize, $test_forced_unit);
    $expected = '1259.52 MB';
    $this->assertEquals($expected, $actual);

    $test_filesize = 1_693_247_906_776;
    $actual = parse_filesize($test_filesize, $test_forced_unit);
    $expected = '1614807.04 MB';
    $this->assertEquals($expected, $actual);
  }

  public function test_should_parse_filesize_with_forced_unit_in_gb() {
    $test_forced_unit = 'GB';

    $test_filesize = 1_864;
    $actual = parse_filesize($test_filesize, $test_forced_unit);
    $expected = '0 GB';
    $this->assertEquals($expected, $actual);

    $test_filesize = 1_321_206;
    $actual = parse_filesize($test_filesize, $test_forced_unit);
    $expected = '0 GB';
    $this->assertEquals($expected, $actual);

    $test_filesize = 1_320_702_444;
    $actual = parse_filesize($test_filesize, $test_forced_unit);
    $expected = '1.23 GB';
    $this->assertEquals($expected, $actual);

    $test_filesize = 1_693_247_906_776;
    $actual = parse_filesize($test_filesize, $test_forced_unit);
    $expected = '1576.96 GB';
    $this->assertEquals($expected, $actual);
  }

  public function test_should_parse_filesize_with_forced_unit_in_tb() {
    $test_forced_unit = 'TB';

    $test_filesize = 1_864;
    $actual = parse_filesize($test_filesize, $test_forced_unit);
    $expected = '0 TB';
    $this->assertEquals($expected, $actual);

    $test_filesize = 1_321_206;
    $actual = parse_filesize($test_filesize, $test_forced_unit);
    $expected = '0 TB';
    $this->assertEquals($expected, $actual);

    $test_filesize = 1_320_702_444;
    $actual = parse_filesize($test_filesize, $test_forced_unit);
    $expected = '0 TB';
    $this->assertEquals($expected, $actual);

    $test_filesize = 1_693_247_906_776;
    $actual = parse_filesize($test_filesize, $test_forced_unit);
    $expected = '1.54 TB';
    $this->assertEquals($expected, $actual);
  }

  public function test_should_parse_filesize_automatically_when_forced_unit_is_invalid() {
    $test_forced_unit = 'invalid';

    $test_filesize = 1_864;
    $actual = parse_filesize($test_filesize, $test_forced_unit);
    $expected = '1.82 KB';
    $this->assertEquals($expected, $actual);

    $test_filesize = 1_321_206;
    $actual = parse_filesize($test_filesize, $test_forced_unit);
    $expected = '1.26 MB';
    $this->assertEquals($expected, $actual);

    $test_filesize = 1_320_702_444;
    $actual = parse_filesize($test_filesize, $test_forced_unit);
    $expected = '1.23 GB';
    $this->assertEquals($expected, $actual);

    $test_filesize = 1_693_247_906_776;
    $actual = parse_filesize($test_filesize, $test_forced_unit);
    $expected = '1.54 TB';
    $this->assertEquals($expected, $actual);
  }

  public function test_should_return_blank_string_on_zero_filesize() {
    $test_filesize = 0;
    $actual = parse_filesize($test_filesize);
    $expected = '';
    $this->assertEquals($expected, $actual);
  }

  public function test_should_return_blank_string_on_null_input() {
    $test_filesize = null;
    $actual = parse_filesize($test_filesize);
    $expected = '';
    $this->assertEquals($expected, $actual);
  }
}
