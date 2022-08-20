<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\ManagementRepository;

class ManagementController extends Controller {

  private ManagementRepository $managementRepository;

  public function __construct(ManagementRepository $managementRepository) {
    $this->managementRepository = $managementRepository;
  }


  /* test */
  /**
   * @api {get} /api/management Retrieve management information
   * @apiName ManagementRetrieve
   * @apiGroup Management
   *
   * @apiHeader {String} Authorization Token received from logging-in
   *
   * @apiSuccess {Number} data.entries Total Entries
   * @apiSuccess {Number} data.buckets Total Buckets
   * @apiSuccess {Number} data.partials Total Partials
   * @apiSuccess {Number} stats.watchSeconds Watch time in seconds
   * @apiSuccess {String} stats.watch Watch time in days
   * @apiSuccess {String} stats.watchSubtext Watch time subtext
   * @apiSuccess {Number} stats.rewatchSeconds Watch with Rewatch time in seconds
   * @apiSuccess {String} stats.rewatch Watch with Rewatch time
   * @apiSuccess {String} stats.rewatchSubtext Watch with Rewatch time subtext
   * @apiSuccess {String} stats.bucketSize Bucket size
   * @apiSuccess {String} stats.entrySize Entry size
   * @apiSuccess {Number} stats.episodes Episode count
   * @apiSuccess {Number} stats.titles Titles count
   * @apiSuccess {Number} stats.seasons Seasons count
   * @apiSuccess {Number} graph.quality_2160 2160p count
   * @apiSuccess {Number} graph.quality_1080 1080p count
   * @apiSuccess {Number} graph.quality_720 720p count
   * @apiSuccess {Number} graph.quality_480 480p count
   * @apiSuccess {Number} graph.quality_360 360p count
   * @apiSuccess {Number} graph.titles_1 Titles watched on January count
   * @apiSuccess {Number} graph.titles_2 Titles watched on February count
   * @apiSuccess {Number} graph.titles_3 Titles watched on March count
   * @apiSuccess {Number} graph.titles_4 Titles watched on April count
   * @apiSuccess {Number} graph.titles_5 Titles watched on May count
   * @apiSuccess {Number} graph.titles_6 Titles watched on June count
   * @apiSuccess {Number} graph.titles_7 Titles watched on July count
   * @apiSuccess {Number} graph.titles_8 Titles watched on August count
   * @apiSuccess {Number} graph.titles_9 Titles watched on September count
   * @apiSuccess {Number} graph.titles_10 Titles watched on October count
   * @apiSuccess {Number} graph.titles_11 Titles watched on November count
   * @apiSuccess {Number} graph.titles_12 Titles watched on December count
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     {
   *       "data": {
   *         "entries": 0,
   *         "buckets": 0,
   *         "partials": 0,
   *       }
   *       "stats": {
   *         "watchSeconds": 0,
   *         "watch": "10 days",
   *         "watchSubtext": "10 hours, 10 minutes, 10 seconds",
   *         "rewatchSeconds": 0,
   *         "rewatch": "10 days",
   *         "rewatchSubtext": "10 hours, 10 minutes, 10 seconds",
   *         "bucketSize": "0 TB",
   *         "entrySize": "0 TB",
   *         "episodes": 0,
   *         "titles": 0,
   *         "seasons": 0,
   *       }
   *       "graph": {
   *         "quality": {,
   *           "quality_2160": 0,
   *           "quality_1080": 0,
   *           "quality_720": 0,
   *           "quality_480": 0,
   *           "quality_360": 0,
   *         },
   *         "months": {,
   *           "jan": 0,
   *           "feb": 0,
   *           "mar": 0,
   *           "apr": 0,
   *           "may": 0,
   *           "jun": 0,
   *           "jul": 0,
   *           "aug": 0,
   *           "sep": 0,
   *           "oct": 0,
   *           "nov": 0,
   *           "dec": 0,
   *         },
   *       }
   *     }
   *
   * @apiError Unauthorized There is no login token provided, or the login token provided is invalid
   */
  public function index(): JsonResponse {
    return response()->json([
      'data' => $this->managementRepository->index(),
    ]);
  }
}
