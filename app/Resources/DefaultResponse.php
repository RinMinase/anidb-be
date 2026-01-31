<?php

namespace App\Resources;

use OpenApi\Attributes as OA;

class DefaultResponse {

  #[OA\Response(
    response: "Success",
    description: "Success",
    content: new OA\JsonContent(
      example: ["status" => 200, "message" => "Success"],
      properties: [
        new OA\Property(property: "status", type: "integer", format: "int32"),
        new OA\Property(property: "message", type: "string"),
      ]
    )
  )]

  #[OA\Schema(
    schema: "DefaultSuccess",
    properties: [
      new OA\Property(property: "status", type: "integer", format: "int32", example: 200),
      new OA\Property(property: "message", type: "string", example: "Success"),
    ]
  )]
  public static function success(string $message = null, array $data = []) {
    $defaultResponse = [
      'status' => 200,
      'message' => $message ?? 'Success',
    ];

    if ($data) {
      $data = convert_array_to_camel_case($data);
    }

    $response = array_merge_recursive($defaultResponse, $data);

    return response()->json($response);
  }
}
