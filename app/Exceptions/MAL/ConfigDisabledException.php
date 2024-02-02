<?php

namespace App\Exceptions\MAL;

use App\Exceptions\CustomException;

/**
 * @OA\Examples(
 *   example="MALConfigDisabledErrorExample",
 *   summary="MAL Config Disabled Error",
 *   value={"status": 500, "message": "Web Scraper is disabled"},
 * ),
 */
class ConfigDisabledException extends CustomException {

  public function render() {
    return response()->json([
      'status' => 500,
      'message' => 'Web Scraper is disabled',
    ], 500);
  }
}
