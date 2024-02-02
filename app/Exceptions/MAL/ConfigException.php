<?php

namespace App\Exceptions\MAL;

use App\Exceptions\CustomException;

/**
 * @OA\Examples(
 *   example="MALConfigErrorExample",
 *   summary="MAL Config Error",
 *   value={"status": 500, "message": "Web Scraper configuration not found"},
 * ),
 */
class ConfigException extends CustomException {

  public function render() {
    return response()->json([
      'status' => 500,
      'message' => 'Web Scraper configuration not found',
    ], 500);
  }
}
