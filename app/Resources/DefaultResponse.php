<?php

namespace App\Resources;

class DefaultResponse {

  public static function success($message = null) {
    return response()->json([
      'status' => 200,
      'message' => $message ?? 'Success',
    ]);
  }
}
