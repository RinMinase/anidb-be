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
   * ),
   * @OA\Schema(
   *   schema="DefaultSuccess",
   *   @OA\Property(property="status", type="integer", format="int32", example=200),
   *   @OA\Property(property="message", type="string", example="Success"),
   * )
   */
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
