<?php

namespace App\Resources;

use OpenApi\Attributes as OA;

class ErrorResponse {

  #[OA\Response(
    response: "BadRequest",
    description: "Bad Request",
    content: new OA\JsonContent(
      example: ["status" => 400, "message" => "There was a problem in parsing your request"],
      properties: [
        new OA\Property(property: "status", type: "integer", format: "int32"),
        new OA\Property(property: "message", type: "string"),
      ]
    )
  )]
  public static function badRequest($message = null) {
    return response()->json([
      'status' => 400,
      'message' => $message ?? 'There was a problem in parsing your request',
    ], 400);
  }

  #[OA\Response(
    response: "Unauthorized",
    description: "Unauthorized",
    content: new OA\JsonContent(
      example: ["status" => 401, "message" => "Unauthorized"],
      properties: [
        new OA\Property(property: "status", type: "integer", format: "int32"),
        new OA\Property(property: "message", type: "string"),
      ]
    )
  )]
  public static function unauthorized($message = null) {
    return response()->json([
      'status' => 401,
      'message' => $message ?? 'Unauthorized',
    ], 401);
  }

  #[OA\Response(
    response: "NotFound",
    description: "Not Found",
    content: new OA\JsonContent(
      example: [
        "status" => 404,
        "message" => "The provided ID is invalid, or the item does not exist",
      ],
      properties: [
        new OA\Property(property: "status", type: "integer", format: "int32"),
        new OA\Property(property: "message", type: "string"),
      ]
    )
  )]
  public static function notFound($message = null) {
    return response()->json([
      'status' => 404,
      'message' => $message ?? 'The provided ID is invalid, or the item does not exist',
    ], 404);
  }

  #[OA\Response(
    response: "Failed",
    description: "Failed",
    content: new OA\JsonContent(
      example: ["status" => 500, "message" => "Failed"],
      properties: [
        new OA\Property(property: "status", type: "integer", format: "int32"),
        new OA\Property(property: "message", type: "string"),
      ]
    )
  )]
  public static function failed($message = null) {
    return response()->json([
      'status' => 500,
      'message' => $message ?? 'An unknown error has occured',
    ], 500);
  }

  #[OA\Response(
    response: "Unavailable",
    description: "Unavailable",
    content: new OA\JsonContent(
      example: ["status" => 503, "message" => "Service unavailable"],
      properties: [
        new OA\Property(property: "status", type: "integer", format: "int32"),
        new OA\Property(property: "message", type: "string"),
      ]
    )
  )]
  public static function unavailable($message = null) {
    return response()->json([
      'status' => 503,
      'message' => $message ?? 'Service unavailable',
    ], 503);
  }
}
