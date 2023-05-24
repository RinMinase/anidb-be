<?php

namespace App\Resources;

class ErrorResponse {

  public static function notFound($message = null) {
    /**
     * @OA\Response(
     *   response="NotFound",
     *   description="Not Found",
     *   @OA\JsonContent(
     *     example={
     *       "status": 404,
     *       "message": "The provided ID is invalid, or the item does not exist",
     *     },
     *     @OA\Property(property="status", type="integer", format="int32"),
     *     @OA\Property(property="message", type="string"),
     *   ),
     * )
     */
    return response()->json([
      'status' => 404,
      'message' => $message ?? 'The provided ID is invalid, or the item does not exist',
    ], 404);
  }

  /**
   * @OA\Response(
   *   response="Failed",
   *   description="Failed",
   *   @OA\JsonContent(
   *     example={"status": 500, "message": "Failed"},
   *     @OA\Property(property="status", type="integer", format="int32"),
   *     @OA\Property(property="message", type="string"),
   *   ),
   * )
   */
  public static function failed($message = null) {
    return response()->json([
      'status' => 500,
      'message' => $message ?? 'An unknown error has occured',
    ], 500);
  }
}
