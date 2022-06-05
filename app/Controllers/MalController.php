<?php

namespace App\Controllers;

use Exception;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\MALEntry;
use App\Models\MALSearch;

class MalController extends Controller {

  protected $scrapeURI;

  public function __construct() {
    $this->scrapeURI = env('SCRAPER_BASE_URI', null);
  }

  public function index($params) {
    if (!env('DISABLE_SCRAPER')) {
      if (env('SCRAPER_BASE_URI')) {
        return $this->scrape($params);
      } else {
        throw new Exception('Web Scraper configuration not found');
      }
    }
  }

  private function scrape($params) {
    if (is_numeric($params)) {
      return $this->getAnime($params);
    } else {
      return $this->searchAnime($params);
    }
  }

  /**
   * @api {get} /api/mal/:id Retrieve Title Information
   * @apiName RetrieveTitleInfo
   * @apiGroup MAL
   *
   * @apiHeader {String} token User login token
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
   * @apiErrorExample Unauthorized
   *     HTTP/1.1 401 Forbidden
   *     {
   *       "status": 401,
   *       "message": "Unauthorized"
   *     }
   *
   * @apiErrorExample ServiceUnavailable
   *     HTTP/1.1 503 Forbidden
   *     {
   *       "status": 503,
   *       "message": "Issues in connecting to MAL Servers"
   *     }
   */
  private function getAnime($id = 37430) {
    try {
      $data = Http::get($this->scrapeURI . '/anime/' . $id)->body();
      $data = MALEntry::parse(new Crawler($data))->get();
    } catch (Exception $e) {
      if (env('APP_DEBUG')) {
        throw new Exception('Issues in connecting to MAL Servers');
      } else {
        return response([
          'status' => 503,
          'message' => 'Issues in connecting to MAL Servers',
        ], 503);
      }
    }

    return response()->json($data);
  }

  /**
   * @api {get} /api/mal/:query Query Titles
   * @apiName QueryTitles
   * @apiGroup MAL
   *
   * @apiHeader {String} token User login token
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
   * @apiErrorExample Unauthorized
   *     HTTP/1.1 401 Forbidden
   *     {
   *       "status": 401,
   *       "message": "Unauthorized"
   *     }
   *
   * @apiErrorExample ServiceUnavailable
   *     HTTP/1.1 503 Forbidden
   *     {
   *       "status": 503,
   *       "message": "Issues in connecting to MAL Servers"
   *     }
   */
  private function searchAnime($query) {
    try {
      $data = Http::get($this->scrapeURI . '/anime.php?q=' . urldecode($query))->body();
      $data = MALSearch::parse(new Crawler($data))->get();
    } catch (Exception $e) {
      if (env('APP_DEBUG')) {
        throw new Exception('Issues in connecting to MAL Servers');
      } else {
        return response([
          'status' => 503,
          'message' => 'Issues in connecting to MAL Servers',
        ], 503);
      }
    }

    return response()->json($data);
  }

}