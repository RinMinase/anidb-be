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
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/MalConfigErrorResponse"),
   *   @OA\Response(response=503, ref="#/components/responses/MalServerErrorResponse"),
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
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/MalConfigErrorResponse"),
   *   @OA\Response(response=503, ref="#/components/responses/MalServerErrorResponse"),
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
 * @OA\Response(
 *   response="MalConfigErrorResponse",
 *   description="MAL Scraper Configuration Error Responses",
 *   @OA\JsonContent(
 *     examples={
 *       @OA\Examples(
 *         summary="Scaper Configuration Not Found",
 *         example="ScaperConfigNotFound",
 *         value={"status": 500, "message": "Web Scraper configuration not found"},
 *       ),
 *       @OA\Examples(
 *         summary="Scraper Disabled",
 *         example="ScraperDisabled",
 *         value={"status": 500, "message": "Web Scraper is disabled"},
 *       ),
 *     },
 *     @OA\Property(property="status", type="integer", format="int32"),
 *     @OA\Property(property="message", type="string"),
 *   ),
 * )
 */
class MalScraperConfigErrorResponse {
}

/**
 * @OA\Response(
 *   response="MalServerErrorResponse",
 *   description="MAL Server Error",
 *   @OA\JsonContent(
 *     example={"status": 503, "message": "Issues in connecting to MAL Servers"},
 *     @OA\Property(property="status", type="integer", format="int32"),
 *     @OA\Property(property="message", type="string"),
 *   ),
 * )
 */
class MalServerErrorResponse {
}
