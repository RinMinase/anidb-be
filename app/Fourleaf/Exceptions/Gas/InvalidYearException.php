<?php

namespace App\Fourleaf\Exceptions\Gas;

use OpenApi\Attributes as OA;

use App\Exceptions\CustomException;

#[OA\Response(
  response: "FourleafGasInvalidYearResponse",
  description: "Fourleaf - Gas Invalid Year Error",
  content: new OA\JsonContent(
    example: ["status" => 401, "message" => "The year is invalid"],
    properties: [
      new OA\Property(property: "status", type: "integer", format: "int32"),
      new OA\Property(property: "message", type: "string")
    ]
  )
)]
class InvalidYearException extends CustomException {

  public function render() {
    return response()->json([
      'status' => 401,
      'message' => 'The year is invalid',
    ], 401);
  }
}
