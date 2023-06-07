<?php

namespace App\Exceptions\Anilist;

use Exception;

/**
 * @OA\Examples(
 *   example="AnilistConfigErrorExample",
 *   summary="Configuration Error",
 *   value={"status": 500, "message": "Anilist scraper configuration not found."},
 * ),
 */
class ConfigException extends Exception {

  public function render() {
    return response()->json([
      'status' => 500,
      'message' => 'Anilist scraper configuration not found.',
    ], 500);
  }
}
