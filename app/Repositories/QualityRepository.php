<?php

namespace App\Repositories;

use App\Models\Quality;

class QualityRepository {

  public function getAll() {
    return Quality::select('id', 'quality')
      ->orderBy('id', 'asc')
      ->get();
  }

  // Helper Functions
  public static function parseQuality(string $quality): string {
    $quality = strtolower($quality);

    $values = ['4k', 'uhd', '2160p', '2160'];
    if (in_array($quality, $values)) {
      return Quality::where('vertical_pixels', 2160)->first()->quality;
    }

    $values = ['fhd', '1080p', '1080'];
    if (in_array($quality, $values)) {
      return Quality::where('vertical_pixels', 1080)->first()->quality;
    }

    $values = ['hd', '720p', '720'];
    if (in_array($quality, $values)) {
      return Quality::where('vertical_pixels', 720)->first()->quality;
    }

    $values = ['hq', '480p', '480'];
    if (in_array($quality, $values)) {
      return Quality::where('vertical_pixels', 480)->first()->quality;
    }

    $values = ['lq', '360p', '360'];
    if (in_array($quality, $values)) {
      return Quality::where('vertical_pixels', 360)->first()->quality;
    }

    return null;
  }
}
