<?php

namespace App\Exceptions;

use OpenApi\Attributes as OA;

#[OA\Response(
  response: "JsonParsingException",
  description: "Partial Parsing Error",
  content: new OA\JsonContent(
    example: ["status" => 400, "message" => "The file is an invalid JSON"],
    properties: [
      new OA\Property(property: "status", type: "integer", format: "int32"),
      new OA\Property(property: "message", type: "string"),
    ]
  )
)]
class JsonParsingException extends CustomException {

  public function render() {
    return response()->json([
      'status' => 400,
      'message' => 'The file is an invalid JSON',
    ], 400);
  }
}
