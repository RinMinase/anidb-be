<?php

namespace App\Exceptions\Mal;

use Exception;

/**
 * @OA\Examples(
 *   example="MalConfigDisabledErrorExample",
 *   summary="MAL Config Disabled Error",
 *   value={"status": 500, "message": "Web Scraper is disabled"},
 * ),
 */
class ConfigDisabledException extends Exception {

  public function render() {
    return response()->json([
      'status' => 500,
      'message' => 'Web Scraper is disabled',
    ], 500);
  }
}
