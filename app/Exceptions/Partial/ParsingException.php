<?php

namespace App\Exceptions\Partial;

use App\Exceptions\CustomException;

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
class ParsingException extends CustomException {

  public ?string $field = null;

  public function __construct(?string $field = null) {
    parent::__construct($field);
    $this->field = $field;
  }

  public function render() {
    if ($this->field !== null) {
      return response()->json([
        'status' => 401,
        'data' => [
          $this->field => [
            'Error in parsing values or length of one or more titles are greater than 256'
          ],
        ],
      ], 401);
    }

    return response()->json([
      'status' => 400,
      'message' => 'There was a problem in parsing your request',
    ], 400);
  }
}
