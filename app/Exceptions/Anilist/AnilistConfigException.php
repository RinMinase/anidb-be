<?php

namespace App\Exceptions\Anilist;

use Exception;

class AnilistConfigException extends Exception {

  public function render() {
    return response()->json([
      'status' => 500,
      'message' => 'Web Scraper configuration not found.',
    ], 500);
  }
}
