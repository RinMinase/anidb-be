<?php

namespace App\Exceptions\Export;

use App\Exceptions\CustomException;

/**
 * @OA\Response(
 *   response="ExportFileIncompleteResponse",
 *   description="Export File Incomplete Error",
 *   @OA\JsonContent(
 *     example={"status": 400, "message": "The file is still incomplete"},
 *     @OA\Property(property="status", type="integer", format="int32"),
 *     @OA\Property(property="message", type="string"),
 *   ),
 * )
 */
class FileIncompleteException extends CustomException {

  public function render() {
    return response()->json([
      'status' => 400,
      'message' => 'The file is still incomplete.',
    ], 400);
  }
}
