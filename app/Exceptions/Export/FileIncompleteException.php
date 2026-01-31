<?php

namespace App\Exceptions\Export;

use OpenApi\Attributes as OA;

use App\Exceptions\CustomException;

#[OA\Response(
  response: "ExportFileIncompleteResponse",
  description: "Export File Incomplete Error",
  content: new OA\JsonContent(
    example: ["status" => 400, "message" => "The file is still incomplete"],
    properties: [
      new OA\Property(property: "status", type: "integer", format: "int32"),
      new OA\Property(property: "message", type: "string"),
    ]
  )
)]
class FileIncompleteException extends CustomException {

  public function render() {
    return response()->json([
      'status' => 400,
      'message' => 'The file is still incomplete.',
    ], 400);
  }
}
