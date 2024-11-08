<?php

namespace App\Exceptions\Entry;

use App\Exceptions\CustomException;

/**
 * @OA\Response(
 *   response="EntryParsingResponse",
 *   description="Partial Parsing Error",
 *   @OA\JsonContent(
 *     example={"status": 401, "message": "There was a problem in parsing your request"},
 *     @OA\Property(property="status", type="integer", format="int32"),
 *     @OA\Property(property="message", type="string"),
 *   ),
 * )
 */
class ParsingException extends CustomException {

  public function render() {
    return response()->json([
      'status' => 401,
      'message' => 'There was a problem in parsing your request',
    ], 401);
  }
}
