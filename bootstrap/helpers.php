<?php

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

if (!function_exists('db_int_max')) {
  function db_int_max(string $type, bool $signed = true) {
    if ($type == "tiny" && $signed) return 255;
    if ($type == "tiny" && !$signed) return 127;

    if ($type == "small" && $signed) return 32767;
    if ($type == "small" && !$signed) return 65535;

    if ($type == "medium" && $signed) return 8388607;
    if ($type == "medium" && !$signed) return 16777215;

    if ($type == "normal" && $signed) return 2147483647;
    if ($type == "normal" && !$signed) return 4294967295;

    if ($type == "bigint" && $signed) return 9223372036854775807;
    if ($type == "bigint" && !$signed) return 18446744073709551615;
  }
}

if (!function_exists('year_validation')) {
  function year_validation(bool $required = false): string {
    if ($required) return 'required|integer|min:1900|max:2999';

    return 'integer|min:1900|max:2999';
  }
}

if (!function_exists('is_json_error_response')) {
  function is_json_error_response(mixed $data): bool {
    if (gettype($data) === "object") {
      return get_class($data) === "Illuminate\Http\JsonResponse";
    }

    return false;
  }
}
