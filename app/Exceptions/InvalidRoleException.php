<?php

namespace App\Exceptions;

use OpenApi\Attributes as OA;

#[OA\Response(
  response: "InvalidRoleException",
  description: "Invaid Role Error",
  content: new OA\JsonContent(
    example: ["status" => 403, "message" => "You should be an admin to access this"],
    properties: [
      new OA\Property(property: "status", type: "integer", format: "int32"),
      new OA\Property(property: "message", type: "string"),
    ]
  )
)]
class InvalidRoleException extends CustomException {

  public function render() {
    return response()->json([
      'status' => 403,
      'message' => 'You should be an admin to access this',
    ], 403);
  }
}
