<?php

namespace App\Exceptions\Auth;

use Exception;

/**
 * @OA\Response(
 *   response="AuthInvalidCredentialsResponse",
 *   description="Invalid Credentials Error",
 *   @OA\JsonContent(
 *     example={"status": 401, "message": "Credentials does not match."},
 *     @OA\Property(property="status", type="integer", format="int32"),
 *     @OA\Property(property="message", type="string"),
 *   ),
 * )
 */
class InvalidCredentialsException extends Exception {

  public function render() {
    return response()->json([
      'status' => 401,
      'message' => 'Credentials does not match.',
    ], 401);
  }
}
