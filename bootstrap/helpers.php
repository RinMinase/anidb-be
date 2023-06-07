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
