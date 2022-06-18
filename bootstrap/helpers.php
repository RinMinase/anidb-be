<?php

if (!function_exists('parse_filesize')) {
  function parse_filesize(int $size): string {
    $KB = 1024;
    $MB = 1024 * $KB;
    $GB = 1024 * $MB;

    if ($size >= $GB) {
      $filesize = round($size / $GB, 2) . " GB";
    } else if ($size >= $MB) {
      $filesize = round($size / $MB, 2) . " MB";
    } else if ($size >= $KB) {
      $filesize = round($size / $KB, 2) . " KB";
    } else {
      $filesize = $size ?? 0 . " B";
    }

    return $filesize;
  }
}
