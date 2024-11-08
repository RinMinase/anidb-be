<?php

namespace App\Exceptions\Entry;

use App\Exceptions\CustomException;

/**
 * @OA\Response(
 *   response="EntryParsingResponse",
 *   description="Partial Parsing Error",
 *   @OA\JsonContent(
 *     example={
 *       "status": 401,
 *       "data": {
 *         "offquel_id": "{{ validation message }}"
 *       }
 *     },
 *     @OA\Property(property="status", type="integer", format="int32"),
 *     @OA\Property(
 *       property="data",
 *       @OA\Property(property="offquel_id", type="string"),
 *     ),
 *   ),
 * )
 */
class ParsingException extends CustomException {

  protected $error_message = '';

  public function __construct($error_message) {
    $this->error_message = $error_message;
  }

  public function render() {
    return response()->json([
      'status' => 401,
      'data' => [
        'offquel_id' => $this->error_message,
      ],
    ], 401);
  }
}
