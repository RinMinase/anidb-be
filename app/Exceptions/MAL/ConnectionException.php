<?php

namespace App\Exceptions\MAL;

use App\Exceptions\CustomException;

/**
 * @OA\Response(
 *   response="MALConnectionResponse",
 *   description="MAL Connection Error",
 *   @OA\JsonContent(
 *     example={"status": 503, "message": "Issues in connecting to MAL Servers"},
 *     @OA\Property(property="status", type="integer", format="int32"),
 *     @OA\Property(property="message", type="string"),
 *   ),
 * )
 */
class ConnectionException extends CustomException {

  public function render() {
    return response()->json([
      'status' => 503,
      'message' => 'Issues in connecting to MAL Servers',
    ], 503);
  }
}
