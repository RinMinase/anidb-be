<?php

namespace App\Resources;

class DefaultResponse {

  /**
   * @OA\Response(
   *   response="Success",
   *   description="Success",
   *   @OA\JsonContent(
   *     example={"status": 200, "message": "Success"},
   *     @OA\Property(property="status", type="integer", format="int32"),
   *     @OA\Property(property="message", type="string"),
   *   ),
   * )
   */
  public static function success($message = null) {
    return response()->json([
      'status' => 200,
      'message' => $message ?? 'Success',
    ]);
  }
}
