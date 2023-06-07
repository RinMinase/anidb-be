<?php

namespace App\Exceptions\Anilist;

use Exception;

class AnilistParsingException extends Exception {

  public function render() {
    return response()->json([
      'status' => 500,
      'message' => 'Issues in parsing AniList response.',
    ], 500);
  }
}
