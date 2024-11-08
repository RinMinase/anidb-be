<?php

namespace App\Exceptions\Entry;

use App\Exceptions\CustomException;

/**
 * @OA\Response(
 *   response="EntryGenreResponse",
 *   description="Invalid Genre Error",
 *   @OA\JsonContent(
 *     example={
 *       "status": 401,
 *       "data": {
 *         "genres": {"{{ validation message }}"}
 *       }
 *     },
 *     @OA\Property(property="status", type="integer", format="int32"),
 *     @OA\Property(
 *       property="data",
 *       @OA\Property(
 *         property="genres",
 *         type="array",
 *         @OA\Items(@OA\Schema(type="string")),
 *       ),
 *     ),
 *   ),
 * )
 */
class InvalidGenreException extends CustomException {

  protected $error_message = '';

  public function __construct($error_message) {
    $this->error_message = $error_message;
  }

  public function render() {
    return response()->json([
      'status' => 401,
      'data' => [
        'genres' => [$this->error_message],
      ],
    ], 401);
  }
}
