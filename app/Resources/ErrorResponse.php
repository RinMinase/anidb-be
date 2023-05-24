<?php

namespace App\Resources;

class ErrorResponse {

  public static function notFound($message = null) {
    return response()->json([
      'status' => 404,
      'message' => $message ?? 'The provided ID is invalid, or the item does not exist',
    ], 404);
  }

  public static function failed($message = null) {
    return response()->json([
      'status' => 500,
      'message' => $message ?? 'An unknown error has occured',
    ], 500);
  }
}
