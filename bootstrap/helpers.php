<?php

if (!function_exists('parse_filesize')) {
  function parse_filesize(int $size, string $forced_unit = null): string {
    $KB = 1024;
    $MB = 1024 * $KB;
    $GB = 1024 * $MB;
    $TB = 1024 * $GB;

    if ($size < $KB) {
      $filesize = $size ?? 0 . " B";
    } else if ($size < $MB || $forced_unit === 'KB') {
      $filesize = round($size / $KB, 2) . " KB";
    } else if ($size < $GB || $forced_unit === 'MB') {
      $filesize = round($size / $MB, 2) . " MB";
    } else if ($size < $TB || $forced_unit === 'GB') {
      $filesize = round($size / $GB, 2) . " GB";
    } else {
      $filesize = round($size / $TB, 2) . " TB";
    }

    return $filesize;
  }
}
