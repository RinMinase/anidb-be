<?php

namespace App\Exceptions\Anilist;

use Exception;

class AnilistRateLimitException extends Exception {

  protected $retrySeconds = 0;

  public function __construct($retrySeconds) {
    $this->retrySeconds = $retrySeconds;
  }

  public function render() {
    return response()->json([
      'status' => 429,
      'message' => 'AniList rate limit was reached. ' .
        'Please retry in ' . $this->retrySeconds . ' seconds.',
    ], 429);
  }
}
