<?php

namespace App\Exceptions\Anilist;

use Exception;

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
class ConnectionException extends Exception {

  public function render() {
    return response()->json([
      'status' => 503,
      'message' => 'Issues in connecting to AniList servers.',
    ], 503);
  }
}
