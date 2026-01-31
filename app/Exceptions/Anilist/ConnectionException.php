<?php

namespace App\Exceptions\Anilist;

use OpenApi\Attributes as OA;

use App\Exceptions\CustomException;

#[OA\Response(
  response: "AnilistConnectionErrorResponse",
  description: "Connection Error",
  content: new OA\JsonContent(
    example: ["status" => 503, "message" => "Issues in connecting to AniList servers"],
    properties: [
      new OA\Property(property: "status", type: "integer", format: "int32"),
      new OA\Property(property: "message", type: "string"),
    ]
  )
)]
class ConnectionException extends CustomException {

  public function render() {
    return response()->json([
      'status' => 503,
      'message' => 'Issues in connecting to AniList servers.',
    ], 503);
  }
}
