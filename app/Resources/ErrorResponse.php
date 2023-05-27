<?php

namespace App\Resources;

class ErrorResponse {

  /**
   * @OA\Response(
   *   response="BadRequest",
   *   description="Bad Request",
   *   @OA\JsonContent(
   *     example={"status": 400, "message": "There was a problem in parsing your request"},
   *     @OA\Property(property="status", type="integer", format="int32"),
   *     @OA\Property(property="message", type="string"),
   *   ),
   * )
   */
  public static function badRequest($message = null) {
    return response()->json([
      'status' => 400,
      'message' => $message ?? 'There was a problem in parsing your request',
    ], 400);
  }

  /**
   * @OA\Response(
   *   response="Unauthorized",
   *   description="Unauthorized",
   *   @OA\JsonContent(
   *     example={"status": 401, "message": "Unauthorized"},
   *     @OA\Property(property="status", type="integer", format="int32"),
   *     @OA\Property(property="message", type="string"),
   *   ),
   * )
   */
  public static function unauthorized($message = null) {
    return response()->json([
      'status' => 401,
      'message' => $message ?? 'Unauthorized',
    ], 401);
  }

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
  public static function notFound($message = null) {
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

  /**
   * @OA\Response(
   *   response="Unavailable",
   *   description="Unavailable",
   *   @OA\JsonContent(
   *     example={"status": 503, "message": "Service unavailable"},
   *     @OA\Property(property="status", type="integer", format="int32"),
   *     @OA\Property(property="message", type="string"),
   *   ),
   * )
   */
  public static function unavailable($message = null) {
    return response()->json([
      'status' => 503,
      'message' => $message ?? 'Service unavailable',
    ], 503);
  }
}
