<?php

namespace App\Exceptions\Anilist;

use App\Exceptions\CustomException;

/**
 * @OA\Response(
 *   response="AnilistConnectionErrorResponse",
 *   description="Connection Error",
 *   @OA\JsonContent(
 *     example={"status": 503, "message": "Issues in connecting to AniList servers"},
 *     @OA\Property(property="status", type="integer", format="int32"),
 *     @OA\Property(property="message", type="string"),
 *   ),
 * )
 */
class ConnectionException extends CustomException {

  public function render() {
    return response()->json([
      'status' => 503,
      'message' => 'Issues in connecting to AniList servers.',
    ], 503);
  }
}
