<?php

namespace App\Exceptions\Export;

use OpenApi\Attributes as OA;

use App\Exceptions\CustomException;

#[OA\Examples(
  example: "ZipFileProcessErrorExample",
  summary: "Export Zip File Process Error",
  value: ["status" => 500, "message" => "Error in processing zip file."]
)]
#[OA\Response(
  response: "ZipFileProcessException",
  description: "Export Zip File Process Error",
  content: new OA\JsonContent(
    example: ["status" => 500, "message" => "Error in processing zip file."],
    properties: [
      new OA\Property(property: "status", type: "integer", format: "int32"),
      new OA\Property(property: "message", type: "string"),
    ]
  )
)]
class ZipFileProcessException extends CustomException {

  public function render() {
    return response()->json([
      'status' => 500,
      'message' => 'Error in processing zip file.',
    ], 500);
  }
}
