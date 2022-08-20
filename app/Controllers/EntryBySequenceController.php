<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\EntryRepository;

class EntryBySequenceController extends Controller {

  private EntryRepository $entryRepository;

  public function __construct(EntryRepository $entryRepository) {
    $this->entryRepository = $entryRepository;
  }


  /**
   * @api {get} /api/entries/by-sequence/:id Retrieve By sequence
   * @apiName RetrieveBySequence
   * @apiGroup Entry
   *
   * @apiHeader {String} token User login token
   * @apiParam {String} id Sequence ID
   *
   * @apiSuccess {Object[]} data Entry data
   * @apiSuccess {UUID} data.id Entry ID
   * @apiSuccess {Date} data.dateFinished Date fisished or date last rewatched
   * @apiSuccess {Number} data.episodes Number of episodes
   * @apiSuccess {String} data.filesize Filesize in nearest byte unit
   * @apiSuccess {Number} data.ovas Number of OVAs
   * @apiSuccess {String='4K 2160p','FHD 1080p','HD 720p','HQ 480p','LQ 360p'} data.quality Video quality
   * @apiSuccess {Boolean} data.rewatched Flag to check if date stated is alread rewatched date
   * @apiSuccess {Number} data.specials Number of specials
   * @apiSuccess {String} data.title Entry title
   * @apiSuccess {Object} stats Stats data
   * @apiSuccess {Number} stats.titles_per_day Titles per day
   * @apiSuccess {Number} stats.eps_per_day Titles per day
   * @apiSuccess {Number} stats.quality_2160 Quality count for 2160p
   * @apiSuccess {Number} stats.quality_1080 Quality count for 1080p
   * @apiSuccess {Number} stats.quality_720 Quality count for 720p
   * @apiSuccess {Number} stats.quality_480 Quality count for 480p
   * @apiSuccess {Number} stats.quality_360 Quality count for 360p
   * @apiSuccess {Number} stats.total_titles Total titles
   * @apiSuccess {Number} stats.total_eps Total episodes
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     {
   *       "data": [
   *         {
   *           "id": "9ef81943-78f0-4d1c-a831-a59fb5af339c"
   *           "dateFinished": "Mar 01, 2011",
   *           "episodes": 25,
   *           "filesize": "10.25 GB",
   *           "ovas": 1,
   *           "quality": "FHD 1080p",
   *           "rewatched": false,
   *           "specials": 1,
   *           "title": "Title"
   *         }, { ... }
   *       ]
   *       "stats": {
   *         "titles_per_day": 0,
   *         "eps_per_day": 0,
   *         "quality_2160": 0,
   *         "quality_1080": 0,
   *         "quality_720": 0,
   *         "quality_480": 0,
   *         "quality_360": 0,
   *         "total_titles": 0,
   *         "total_eps": 0,
   *         "total_size": "0 GB",
   *         "total_days": 0,
   *         "start_date": "Jan 01, 2000",
   *         "end_date": "Feb 01, 2000"
   *       }
   *     }
   *
   * @apiError Unauthorized There is no login token provided, or the login token provided is invalid
   */
  public function index($id): JsonResponse {
    return response()->json([
      'data' => $this->entryRepository->getBySequence($id),
    ]);
  }
}
