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
    } catch (Exception) {
      return response()->json([
        'status' => 503,
        'message' => 'Issues in connecting to MAL Servers',
      ], 503);
    }
  }

  /**
   * @api {get} /api/mal/:query Query Titles
   * @apiName QueryTitles
   * @apiGroup MAL
   *
   * @apiHeader {String} Authorization Token received from logging-in
   * @apiParam {String} query Query string to match
   *
   * @apiSuccess {Object[]} data MAL Title ID
   * @apiSuccess {String} data.id MAL title id
   * @apiSuccess {String} data.title Full title
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     [
   *       {
   *         "id": "37430",
   *         "title": "Tensei shitara Slime Datta Ken",
   *       },
   *       {
   *         "id": "8475",
   *         "title": "Asura",
   *       }, { ... }
   *     ]
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
    } catch (Exception) {
      return response()->json([
        'status' => 503,
        'message' => 'Issues in connecting to MAL Servers',
      ], 503);
    }

    return response()->json($data);
  }
}
