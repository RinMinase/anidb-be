<?php

namespace App\Exceptions\Entry;

use OpenApi\Attributes as OA;

use App\Exceptions\CustomException;

#[OA\Response(
  response: "EntryGenreResponse",
  description: "Invalid Genre Error",
  content: new OA\JsonContent(
    example: [
      "status" => 401,
      "data" => ["genres" => ["{{ validation message }}"]]
    ],
    properties: [
      new OA\Property(property: "status", type: "integer", format: "int32"),
      new OA\Property(
        property: "data",
        properties: [
          new OA\Property(
            property: "genres",
            type: "array",
            items: new OA\Items(type: "string")
          ),
        ]
      ),
    ]
  )
)]
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
