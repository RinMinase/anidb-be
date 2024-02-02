<?php

namespace App\Exceptions\Anilist;

use App\Exceptions\CustomException;

/**
 * @OA\Response(
 *   response="AnilistRateLimitErrorResponse",
 *   description="Rate Limit Error",
 *   @OA\JsonContent(
 *     example={
 *       "status": 429,
 *       "message": "AniList rate limit was reached. Please retry in ## seconds.",
 *     },
 *     @OA\Property(property="status", type="integer", format="int32"),
 *     @OA\Property(property="message", type="string"),
 *   ),
 * )
 */
class RateLimitException extends CustomException {

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
