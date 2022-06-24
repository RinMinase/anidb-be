<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\EntryRepository;
use App\Resources\Entry\EntryCollection;

class EntryByNameController extends Controller {

  private EntryRepository $entryRepository;

  public function __construct(EntryRepository $entryRepository) {
    $this->entryRepository = $entryRepository;
  }


  /**
   * @api {get} /api/entries/by-name Retrieve By Name Stats
   * @apiName RetrieveByNameStats
   * @apiGroup Entry
   *
   * @apiHeader {String} token User login token
   *
   * @apiSuccess {Object} data By name data
   * @apiSuccess {Object} data.letter Letter of each alphabet
   * @apiSuccess {Number} data.letter.titles Count of titles on this letter
   * @apiSuccess {Number} data.letter.filesize Count of filesize on this letter
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     {
   *       "#" {
   *         "titles": 12,
   *         "filesize": "10.25 GB",
   *       },
   *       "A" {
   *         "titles": 34,
   *         "filesize": "12.23 GB",
   *       },
   *       "B" {
   *         "titles": 34,
   *         "filesize": "12.23 GB",
   *       }, { ... }
   *     }
   *
   * @apiError Unauthorized There is no login token provided, or the login token provided is invalid
   */
  public function index(): JsonResponse {
    return response()->json([
      'data' => $this->entryRepository->getByName(),
    ]);
  }


  /**
   * @api {get} /api/entries/by-name/:letter Retrieve By Letter
   * @apiName RetrieveByLetter
   * @apiGroup Entry
   *
   * @apiHeader {String} token User login token
   * @apiParam {String} letter Letter of the alphabet
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
  public function get($letter): JsonResponse {
    return response()->json([
      'data' => EntryCollection::collection($this->entryRepository->getByLetter($letter)),
    ]);
  }
}