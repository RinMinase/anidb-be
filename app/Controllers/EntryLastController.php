<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\EntryRepository;
use App\Resources\Entry\EntryCollection;

class EntryLastController extends Controller {

  private EntryRepository $entryRepository;

  public function __construct(EntryRepository $entryRepository) {
    $this->entryRepository = $entryRepository;
  }


  /**
   * @api {get} /api/entries/last Retrieve Latest Entries
   * @apiName RetrieveLatestEntry
   * @apiGroup Entry
   *
   * @apiHeader {String} token User login token
   *
   * @apiSuccess {Object[]} data Entry data
   * @apiSuccess {UUID} data.id Entry ID
   * @apiSuccess {Date} data.dateFinished Date fisished or date last rewatched
   * @apiSuccess {String} data.encoder Title encoder
   * @apiSuccess {Number} data.episodes Number of episodes
   * @apiSuccess {String} data.filesize Filesize in nearest byte unit
   * @apiSuccess {Number} data.ovas Number of OVAs
   * @apiSuccess {String='4K 2160p','FHD 1080p','HD 720p','HQ 480p','LQ 360p'} data.quality Video quality
   * @apiSuccess {Number} data.rating Averaged rating of Audio, Enjoyment, Graphics and Plot
   * @apiSuccess {String} data.release Season and year in which the title was released
   * @apiSuccess {String} data.remarks Any remarks for the title
   * @apiSuccess {Boolean} data.rewatched Flag to check if date stated is alread rewatched date
   * @apiSuccess {Number} data.specials Number of specials
   * @apiSuccess {String} data.title Entry title
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     [
   *       {
   *         "id": "9ef81943-78f0-4d1c-a831-a59fb5af339c"
   *         "dateFinished": "Mar 01, 2011",
   *         "encoder": "encoder—encoder2",
   *         "episodes": 25,
   *         "filesize": "10.25 GB",
   *         "ovas": 1,
   *         "quality": "FHD 1080p",
   *         "rating": 7.5,
   *         "release": "Spring 2017",
   *         "remarks": "some remarks",
   *         "specials": 1,
   *         "title": "Title"
   *       }, { ... }
   *     ]
   *
   * @apiError Unauthorized There is no login token provided, or the login token provided is invalid
   */
  public function index(): JsonResponse {
    return response()->json([
      'data' => EntryCollection::collection($this->entryRepository->getLast()),
    ]);
  }
}
