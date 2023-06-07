<?php

namespace App\Exceptions\Anilist;

use Exception;

/**
 * @OA\Examples(
 *   example="AnilistParsingErrorExample",
 *   summary="Parsing Error",
 *   value={"status": 500, "message": "Issues in parsing AniList response."},
 * ),
 */
class ParsingException extends Exception {

  public function render() {
    return response()->json([
      'status' => 500,
      'message' => 'Issues in parsing AniList response.',
    ], 500);
  }
}
