<?php

namespace App\Exceptions\Anilist;

use App\Exceptions\CustomException;

/**
 * @OA\Examples(
 *   example="AnilistParsingErrorExample",
 *   summary="Parsing Error",
 *   value={"status": 500, "message": "Issues in parsing AniList response."},
 * ),
 */
class ParsingException extends CustomException {

  public function render() {
    return response()->json([
      'status' => 500,
      'message' => 'Issues in parsing AniList response.',
    ], 500);
  }
}
