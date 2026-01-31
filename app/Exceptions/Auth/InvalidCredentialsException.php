<?php

namespace App\Exceptions\Auth;

use OpenApi\Attributes as OA;

use App\Exceptions\CustomException;

#[OA\Response(
  response: "AuthInvalidCredentialsResponse",
  description: "Invalid Credentials Error",
  content: new OA\JsonContent(
    example: ["status" => 401, "message" => "Credentials does not match."],
    properties: [
      new OA\Property(property: "status", type: "integer", format: "int32"),
      new OA\Property(property: "message", type: "string"),
    ]
  )
)]
class InvalidCredentialsException extends CustomException {

  public function render() {
    return response()->json([
      'status' => 401,
      'message' => 'Credentials does not match.',
    ], 401);
  }
}
