<?php

namespace App\Exceptions\Mal;

use Exception;

/**
 * @OA\Examples(
 *   example="MalConfigErrorExample",
 *   summary="MAL Config Error",
 *   value={"status": 500, "message": "Web Scraper configuration not found"},
 * ),
 */
class ConfigException extends Exception {

  public function render() {
    return response()->json([
      'status' => 500,
      'message' => 'Web Scraper configuration not found',
    ], 500);
  }
}
