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
   * @apiSuccess {String} data.year Year the count belongs to
   * @apiSuccess {Object} data.seasons Year the count belongs to
   * @apiSuccess {Number} data.seasons.None No-season entries in the year
   * @apiSuccess {Number} data.seasons.Winter Winter entries in the year
   * @apiSuccess {Number} data.seasons.Spring Spring entries in the year
   * @apiSuccess {Number} data.seasons.Summer Summer entries in the year
   * @apiSuccess {Number} data.seasons.Fall Fall entries in the year
   * @apiSuccess {Number} data.count Only present in first NO-year ANY-season item
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     [
   *       {
   *         "year": null,
   *         "seasons": null,
   *         "count": 2
   *       },
   *       {
   *         "year": 2010,
   *         "seasons": {
   *           Spring: 1,
   *           Summer: 2,
   *         },
   *         "count": null
   *       },
   *       {
   *         "year": 2020,
   *         "seasons": {
   *           None: 1,
   *           Winter: 2,
   *         },
   *         "count": null
   *       },
   *     ]
   *
   * @apiError Unauthorized There is no login token provided, or the login token provided is invalid
   */
  public function index(): JsonResponse {
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
  public function get($year): JsonResponse {
    return response()->json([
      'data' => $this->entryRepository->getBySeason($year),
    ]);
  }
}
