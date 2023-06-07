<?php

namespace App\Exceptions\Anilist;

use Exception;

class AnilistConnectionException extends Exception {

  public function render() {
    return response()->json([
      'status' => 503,
      'message' => 'Issues in connecting to AniList Servers.',
    ], 503);
  }
}
