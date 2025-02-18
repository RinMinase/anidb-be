<?php

namespace App\Exceptions;

/**
 * @OA\Response(
 *   response="InvalidRoleException",
 *   description="Invaid Role Error",
 *   @OA\JsonContent(
 *     example={"status": 403, "message": "You should be an admin to access this"},
 *     @OA\Property(property="status", type="integer", format="int32"),
 *     @OA\Property(property="message", type="string"),
 *   ),
 * )
 */
class InvalidRoleException extends CustomException {

  public function render() {
    return response()->json([
      'status' => 403,
      'message' => 'You should be an admin to access this',
    ], 403);
  }
}
