<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\EntryRepository;

class EntryByYearController extends Controller {

  private EntryRepository $entryRepository;

  public function __construct(EntryRepository $entryRepository) {
    $this->entryRepository = $entryRepository;
  }


  /**
   * @api {get} /api/entries/by-year Retrieve By Year Stats
   * @apiName RetrieveByYearStats
   * @apiGroup Entry
   *
   * @apiHeader {String} token User login token
   *
   * @apiSuccess {Object[]} data By Year Data
   * @apiSuccess {String} data.release_season Season the count belongs to
   * @apiSuccess {String} data.release_year Year the count belongs to
   * @apiSuccess {Number} data.count Count entries per year per season
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     [
   *       {
   *         "release_season": null,
   *         "release_year": null,
   *         "count": 1
   *       },
   *       {
   *         "release_season": null,
   *         "release_year": 2010,
   *         "count": 10
   *       },
   *       {
   *         "release_season": "Winter",
   *         "release_year": 2010,
   *         "count": 2
   *       },
   *       {
   *         "release_season": "Spring",
   *         "release_year": 2010,
   *         "count": 3
   *       },
   *       {
   *         "release_season": "Winter",
   *         "release_year": 2000,
   *         "count": 12
   *       }
   *     ]
   *
   * @apiError Unauthorized There is no login token provided, or the login token provided is invalid
   */
  public function getByYear(): JsonResponse {
    return response()->json([
      'data' => $this->entryRepository->getByYear(),
    ]);
  }


  /**
   * @api {get} /api/entries/by-year/:year Retrieve By Season Entries
   * @apiName RetrieveBySeason
   * @apiGroup Entry
   *
   * @apiHeader {String} token User login token
   * @apiParam {Number} year Release year
   *
   * @apiSuccess {Object} data By season data
   * @apiSuccess {Object} data.season Entry data per season
   * @apiSuccess {UUID} data.season.id Entry ID
   * @apiSuccess {String} data.season.title Entry title
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     {
   *       "Winter": [
   *         {
   *           "id": "9ef81943-78f0-4d1c-a831-a59fb5af339c"
   *           "title": "Title"
   *         }, { ... }
   *       ],
   *       "Spring": [],
   *       "Summer": [],
   *       "Fall": [],
   *       "Uncategorized": [],
   *     }
   *
   * @apiError Unauthorized There is no login token provided, or the login token provided is invalid
   */
  public function getBySeason($year): JsonResponse {
    return response()->json([
      'data' => $this->entryRepository->getBySeason($year),
    ]);
  }
}
