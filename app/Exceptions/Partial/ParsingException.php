<?php

namespace App\Exceptions\Partial;

use Exception;

/**
 * @OA\Response(
 *   response="PartialParsingResponse",
 *   description="Partial Parsing Error",
 *   @OA\JsonContent(
 *     example={"status": 400, "message": "There was a problem in parsing your request"},
 *     @OA\Property(property="status", type="integer", format="int32"),
 *     @OA\Property(property="message", type="string"),
 *   ),
 * )
 */
class ParsingException extends Exception {

  public function render() {
    return response()->json([
      'status' => 400,
      'message' => 'There was a problem in parsing your request',
    ], 400);
  }
}
