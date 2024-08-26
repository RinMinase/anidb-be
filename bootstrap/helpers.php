<?php

use Illuminate\Support\Str;

use App\Enums\IntegerSizesEnum;
use App\Enums\IntegerTypesEnum;

if (!function_exists('vdd')) {
  function vdd(mixed ...$value) {
    var_dump(...$value);
    die;
  }
}

if (!function_exists('parse_filesize')) {
  function parse_filesize(?int $size, string $forced_unit = null): string {
    $KB = 1024;
    $MB = 1024 * $KB;
    $GB = 1024 * $MB;
    $TB = 1024 * $GB;

    if ($size === 0 || empty($size)) {
      return "";
    }

    if ($size < $KB) {
      $filesize = $size ?? 0 . " B";
    } else if ($size < $MB) {
      $filesize = round($size / $KB, 2) . " KB";
    } else if ($size < $GB) {
      $filesize = round($size / $MB, 2) . " MB";
    } else if ($size < $TB) {
      $filesize = round($size / $GB, 2) . " GB";
    } else {
      $filesize = round($size / $TB, 2) . " TB";
    }

    if ($forced_unit === 'KB') {
      $filesize = round($size / $KB, 2) . " KB";
    } else if ($forced_unit === 'MB') {
      $filesize = round($size / $MB, 2) . " MB";
    } else if ($forced_unit === 'GB') {
      $filesize = round($size / $GB, 2) . " GB";
    } else if ($forced_unit === 'TB') {
      $filesize = round($size / $TB, 2) . " TB";
    }

    return $filesize;
  }
}

if (!function_exists('is_json')) {
  function is_json(string $json_string): bool {
    json_decode($json_string);

    return json_last_error() === JSON_ERROR_NONE;
  }
}

if (!function_exists('to_boolean')) {
  function to_boolean($value) {
    return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
  }
}

if (!function_exists('convert_array_to_camel_case')) {
  function convert_array_to_camel_case(array $values): array {
    $array_string = json_encode($values);
    $converted = preg_replace_callback('/"(\w+)":/', function ($matches) {
      $key = $matches[1];
      $converted_key = Str::camel($key);

      return '"' . $converted_key . '":';
    }, $array_string);

    return json_decode($converted, true);
  }
}

if (!function_exists('rand_str')) {
  function rand_str(int $length = 20, bool $alpha_only = false): string {
    if ($alpha_only) {
      return chr(rand(97, 122));
    }

    return Str::random($length);
  }
}

if (!function_exists('max_int')) {
  function max_int(
    IntegerTypesEnum $type = IntegerTypesEnum::SIGNED,
    IntegerSizesEnum $size = IntegerSizesEnum::DEFAULT,
    bool $is_negative = false,
  ) {
    if ($type === IntegerTypesEnum::SIGNED) {
      if ($size === IntegerSizesEnum::TINY) return $is_negative ? -127 : 127;
      if ($size === IntegerSizesEnum::SMALL) return $is_negative ? -32767 : 32767;
      if ($size === IntegerSizesEnum::MEDIUM) return $is_negative ? -8388607 : 8388607;

      if ($size === IntegerSizesEnum::BIG) {
        return $is_negative ? -9223372036854775807 : 9223372036854775807;
      }

      return $is_negative ? -2147483647 : 2147483647;
    } else {
      if ($size === IntegerSizesEnum::TINY) return 255;
      if ($size === IntegerSizesEnum::SMALL) return 65535;
      if ($size === IntegerSizesEnum::MEDIUM) return 16777215;
      if ($size === IntegerSizesEnum::BIG) return 18446744073709551615;

      return 4294967295;
    }
  }
}

if (!function_exists('parse_comparator')) {
  function parse_comparator(string $comparator_text): string {
    $valid_comparators = ['>', '>=', '<', '<='];
    if (in_array($comparator_text, $valid_comparators)) {
      return $comparator_text;
    }

    if ($comparator_text === 'gt' || $comparator_text === 'greater than') {
      return '>';
    } else if (
      $comparator_text === 'gte' ||
      $comparator_text === 'greater than or equal' ||
      $comparator_text === 'greater than equal'
    ) {
      return '>=';
    } else if ($comparator_text === 'lt' || $comparator_text === 'less than') {
      return '<';
    } else if (
      $comparator_text === 'lte' ||
      $comparator_text === 'less than or equal' ||
      $comparator_text === 'less than equal'
    ) {
      return '<=';
    } else {
      return '';
    }
  }
}

if (!function_exists('get_comparator')) {
  function get_comparator(string $comparator_text) {
    $comparators = [
      'greater than or equal',
      'greater than equal',
      'greater than',
      'less than or equal',
      'less than equal',
      'less than',
      'gte',
      'gt',
      'lte',
      'lt',
      '>=',
      '<=',
      '>',
      '<',
    ];

    $comparator_text = strtolower($comparator_text);

    foreach ($comparators as $comparator) {
      $index = strpos($comparator_text, $comparator);
      if ($index !== false) {
        // Check if the right side of comparator text is a space
        // Check if the comparator has no left side
        if ($comparator_text[$index + strlen($comparator)] === ' ' && $index === 0) {
          return $comparator;
        }
      }
    }

    throw new Error('Error parsing comparator');
  }
}
