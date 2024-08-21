<?php

namespace App\Exceptions;

/**
 * @OA\Response(
 *   response="JsonParsingException",
 *   description="Partial Parsing Error",
 *   @OA\JsonContent(
 *     example={"status": 400, "message": "The file is an invalid JSON"},
 *     @OA\Property(property="status", type="integer", format="int32"),
 *     @OA\Property(property="message", type="string"),
 *   ),
 * )
 */
class JsonParsingException extends CustomException {

  public function render() {
    return response()->json([
      'status' => 400,
      'message' => 'The file is an invalid JSON',
    ], 400);
  }
}
