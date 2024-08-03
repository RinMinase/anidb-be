<?php

use Illuminate\Support\Str;

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

if (!function_exists('vdd')) {
  function vdd(mixed ...$value) {
    var_dump(...$value);
    die;
  }
}

if (!function_exists('jdd')) {
  function jdd(mixed ...$value) {
    var_dump(json_encode($value, JSON_PRETTY_PRINT));
    die;
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
