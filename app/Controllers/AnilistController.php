<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Repositories\AnilistRepository;
use App\Resources\Anilist\AnilistCollection;
use App\Resources\Anilist\AnilistResource;
use App\Resources\ErrorResponse;

class AnilistController extends Controller {

  private AnilistRepository $anilistRepository;

  public function __construct(AnilistRepository $anilistRepository) {
    $this->anilistRepository = $anilistRepository;
  }

  /**
   * @OA\Get(
   *   tags={"AniList"},
   *   path="/api/anilist/title/{title_id}",
   *   summary="Retrieve Title Information",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="title_id",
   *     description="Title ID",
   *     in="path",
   *     required=true,
   *     example="101280",
   *     @OA\Schema(type="integer", format="int32"),
   *   ),
   *
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(ref="#/components/schemas/AnilistResource"),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=429, ref="#/components/responses/AnilistRateLimitErrorResponse"),
   *   @OA\Response(response=500, ref="#/components/responses/AnilistConfigErrorResponse"),
   *   @OA\Response(response=503, ref="#/components/responses/AnilistServerErrorResponse"),
   * )
   */
  public function get($id = 101280): JsonResponse {
    if (!config('app.anilist_base_uri')) {
      return ErrorResponse::failed('Anilist Scraper configuration not found');
    }

    $data = $this->anilistRepository->get($id);

    if (is_json_error_response($data)) {
      return $data;
    }

    $data = $data['Media'];

    return response()->json([
      'data' => new AnilistResource($data),
    ]);
  }

  /**
   * @OA\Get(
   *   tags={"AniList"},
   *   path="/api/anilist/search",
   *   summary="Query Titles",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="query",
   *     description="Title Search String",
   *     in="query",
   *     required=true,
   *     example="tensei",
   *     @OA\Schema(type="string"),
   *   ),
   *
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(ref="#/components/schemas/AnilistCollection"),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=429, ref="#/components/responses/AnilistRateLimitErrorResponse"),
   *   @OA\Response(response=500, ref="#/components/responses/AnilistConfigErrorResponse"),
   *   @OA\Response(response=503, ref="#/components/responses/AnilistServerErrorResponse"),
   * )
   */
  public function search(Request $request): JsonResponse {
    if (!config('app.scraper.base_uri')) {
      return ErrorResponse::failed('Web Scraper configuration not found');
    }

    $data = $this->anilistRepository->search($request->only('query'));
    $data = collect($data['Page']['media']);

    return response()->json([
      'data' => AnilistCollection::collection($data),
    ]);
  }
}

/**
 * @OA\Response(
 *   response="AnilistConfigErrorResponse",
 *   description="Anilist Scraper Configuration Error Responses",
 *   @OA\JsonContent(
 *     examples={
 *       @OA\Examples(
 *         summary="Scaper Configuration Not Found",
 *         example="ScaperConfigNotFound",
 *         value={"status": 500, "message": "Anilist Scraper configuration not found"},
 *       ),
 *       @OA\Examples(
 *         summary="Parsing error",
 *         example="ParsingError",
 *         value={"status": 500, "message": "Issues in parsing AniList response"},
 *       ),
 *     },
 *     @OA\Property(property="status", type="integer", format="int32"),
 *     @OA\Property(property="message", type="string"),
 *   ),
 * )
 */
class AnilistScraperConfigErrorResponse {
}

/**
 * @OA\Response(
 *   response="AnilistRateLimitErrorResponse",
 *   description="AniList Rate Limit Error",
 *   @OA\JsonContent(
 *     example={"status": 429, "message": "AniList rate limit was reached. Please retry in ## seconds."},
 *     @OA\Property(property="status", type="integer", format="int32"),
 *     @OA\Property(property="message", type="string"),
 *   ),
 * )
 */
class AnilistRateLimitErrorResponse {
}

/**
 * @OA\Response(
 *   response="AnilistServerErrorResponse",
 *   description="AniList Server Error",
 *   @OA\JsonContent(
 *     example={"status": 503, "message": "Issues in connecting to MAL Servers"},
 *     @OA\Property(property="status", type="integer", format="int32"),
 *     @OA\Property(property="message", type="string"),
 *   ),
 * )
 */
class AnilistServerErrorResponse {
}
