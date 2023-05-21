<?php

namespace App\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

use App\Models\MALEntry;
use App\Models\MALSearch;

class MalController extends Controller {

  protected $scrapeURI;

  public function __construct() {
    $this->scrapeURI = env('SCRAPER_BASE_URI', null);
  }

  public function index($params): JsonResponse {
    if (!env('DISABLE_SCRAPER')) {
      if (env('SCRAPER_BASE_URI')) {
        if (is_numeric($params)) {
          return $this->getAnime($params);
        } else {
          return $this->searchAnime($params);
        }
      } else {
        return response()->json([
          'status' => 500,
          'message' => 'Web Scraper configuration not found',
        ], 500);
      }
    } else {
      return response()->json([
        'status' => 500,
        'message' => 'Web Scraper is disabled',
      ], 500);
    }
  }

  /**
   * @api {get} /api/mal/:id Retrieve Title Information
   * @apiName RetrieveTitleInfo
   * @apiGroup MAL
   *
   * @apiHeader {String} Authorization Token received from logging-in
   * @apiParam {Number} id MAL Title ID
   *
   * @apiSuccess {String} url MAL Title URL
   * @apiSuccess {String} title Full title
   * @apiSuccess {String} synonyms Variants
   * @apiSuccess {Number} episodes Number of episodes
   * @apiSuccess {String} premiered Premiered Season and Year
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     {
   *       "url": "https://myanimelist.net/anime/37430/Tensei_shitara_Slime_Datta_Ken",
   *       "title": "Tensei shitara Slime Datta Ken",
   *       "synonyms": "TenSura",
   *       "episodes": 24,
   *       "premiered": "Fall 2018",
   *     }
   *
   * @apiError Unauthorized There is no login token provided, or the login token provided is invalid
   * @apiError (Error 5xx) ServiceUnavailable There is no login token provided, or the login token provided is invalid
   *
   * @apiErrorExample ServiceUnavailable
   *     HTTP/1.1 503 Forbidden
   *     {
   *       "status": 503,
   *       "message": "Issues in connecting to MAL Servers"
   *     }
   */
  /**
   * @OA\Get(
   *   tags={"MAL"},
   *   path="/api/mal/{title_id}",
   *   summary="Retrieve Title Information",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="title_id",
   *     description="Title ID",
   *     in="path",
   *     required=true,
   *     example="39535",
   *     @OA\Schema(type="integer", format="int64"),
   *   ),
   *
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(ref="#/components/schemas/MALEntry"),
   *   ),
   *   @OA\Response(
   *     response=401,
   *     description="Unauthorized",
   *     @OA\JsonContent(ref="#/components/schemas/Unauthorized"),
   *   ),
   *   @OA\Response(
   *     response=500,
   *     description="Scaper Configuration Error",
   *     @OA\JsonContent(ref="#/components/schemas/MalScraperConfigErrorResponse"),
   *   ),
   *   @OA\Response(
   *     response=503,
   *     description="MAL Server Error",
   *     @OA\JsonContent(ref="#/components/schemas/MalServerErrorResponse"),
   *   ),
   * )
   */
  private function getAnime($id = 37430): JsonResponse {
    try {
      $response = Http::get($this->scrapeURI . '/anime/' . $id);

      if ($response->status() >= 500) {
        // Temporary response, will be changed to backup scraper
        return response()->json([
          'status' => 503,
          'message' => 'Issues in connecting to MAL Servers',
        ], 503);
      }

      $data = $response->body();
      $data = MALEntry::parse(new Crawler($data))->get();

      return response()->json($data);
    } catch (Exception) {
      return response()->json([
        'status' => 503,
        'message' => 'Issues in connecting to MAL Servers',
      ], 503);
    }
  }

  /**
   * @OA\Get(
   *   tags={"MAL"},
   *   path="/api/mal/{query_string}",
   *   summary="Query Titles",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="query_string",
   *     description="Title Search String",
   *     in="path",
   *     required=true,
   *         example="tensei",
   *     @OA\Schema(type="string"),
   *   ),
   *
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(ref="#/components/schemas/MALSearch"),
   *   ),
   *   @OA\Response(
   *     response=401,
   *     description="Unauthorized",
   *     @OA\JsonContent(ref="#/components/schemas/Unauthorized"),
   *   ),
   *   @OA\Response(
   *     response=500,
   *     description="Scaper Configuration Error",
   *     @OA\JsonContent(
   *       ref="#/components/schemas/MalScraperConfigErrorResponse",
   *       examples={
   *         @OA\Examples(
   *           example="ScaperConfigNotFound",
   *           ref="#/components/examples/ScaperConfigNotFound",
   *         ),
   *         @OA\Examples(
   *           example="ScraperDisabled",
   *           ref="#/components/examples/ScraperDisabled",
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(
   *     response=503,
   *     description="MAL Server Error",
   *     @OA\JsonContent(ref="#/components/schemas/MalServerErrorResponse"),
   *   ),
   * )
   */
  private function searchAnime($query): JsonResponse {
    try {
      $response = Http::get($this->scrapeURI . '/anime.php?q=' . urldecode($query));

      if ($response->status() >= 500) {
        // Temporary response, will be changed to backup scraper
        return response()->json([
          'status' => 503,
          'message' => 'Issues in connecting to MAL Servers',
        ], 503);
      }

      $data = $response->body();
      $data = MALSearch::parse(new Crawler($data))->get();

      return response()->json($data);
    } catch (Exception) {
      return response()->json([
        'status' => 503,
        'message' => 'Issues in connecting to MAL Servers',
      ], 503);
    }
  }
}

/**
 * @OA\Schema(
 *   schema="MalScraperConfigErrorResponse",
 *   title="500 Scraper Configuration Error Responses",
 *   @OA\Property(property="status", type="string"),
 *   @OA\Property(property="message", type="string"),
 * )
 *
 * @OA\Examples(
 *   summary="Scaper Configuration Not Found",
 *   example="ScaperConfigNotFound",
 *   value={"status": 500, "message": "Web Scraper configuration not found"},
 * ),
 *
 * @OA\Examples(
 *   summary="Scraper Disabled",
 *   example="ScraperDisabled",
 *   value={"status": 500, "message": "Web Scraper is disabled"},
 * ),
 */
class MalScraperConfigErrorResponse {
}

/**
 * @OA\Schema(
 *   schema="MalServerErrorResponse",
 *   title="503 MAL Server Error",
 *   example={"status": "503", "message": "Issues in connecting to MAL Servers"},
 *   @OA\Property(property="status", type="integer", format="int32"),
 *   @OA\Property(property="message", type="string"),
 * )
 */
class MalServerErrorResponse {
}
