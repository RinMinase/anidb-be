<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Repositories\EntryRepository;

class EntryByBucketController extends Controller {

  private EntryRepository $entryRepository;

  public function __construct(EntryRepository $entryRepository) {
    $this->entryRepository = $entryRepository;
  }


  /**
   * @api {get} /api/entries/by-bucket Retrieve By Buckets
   * @apiName RetrieveByBuckets
   * @apiGroup Entry
   *
   * @apiHeader {String} token User login token
   *
   * @apiSuccess {Object[]} data Buckets with entries data
   * @apiSuccess {UUID} data.id Entry ID
   * @apiSuccess {String} data.from Filesize in nearest byte unit
   * @apiSuccess {String} data.free Filesize in nearest byte unit
   * @apiSuccess {String} data.freeTB Filesize in nearest byte unit
   * @apiSuccess {String} data.used Filesize in nearest byte unit
   * @apiSuccess {Number} data.percent Filesize in nearest byte unit
   * @apiSuccess {String} data.total Filesize in nearest byte unit
   * @apiSuccess {Number} data.rawTotal Filesize in nearest byte unit
   * @apiSuccess {Number} data.titles Filesize in nearest byte unit
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     [
   *       {
   *         "id": null,
   *         "from": null,
   *         "to": null,
   *         "free": "1.11 TB",
   *         "freeTB": "1.11 TB",
   *         "used": "123.12 GB",
   *         "percent": 10,
   *         "total": "1.23 TB",
   *         "rawTotal": 1000169533440,
   *         "titles": 1
   *       }, {
   *         "id": 1,
   *         "from": "a",
   *         "to": "d",
   *         "free": "1.11 TB",
   *         "freeTB": null,
   *         "used": "123.12 GB",
   *         "percent": 10,
   *         "total": "1.23 TB",
   *         "rawTotal": 1000169533440,
   *         "titles": 1
   *       }, { ... }
   *     ]
   *
   * @apiError Unauthorized There is no login token provided, or the login token provided is invalid
   */
  public function index(): JsonResponse {
    return response()->json([
      'data' => $this->entryRepository->getBuckets(),
    ]);
  }


  /**
   * @api {get} /api/entries/by-bucket/:id Retrieve By Buckets with Entries
   * @apiName RetrieveByBucketsWithEntries
   * @apiGroup Entry
   *
   * @apiHeader {String} token User login token
   * @apiParam {Number} id Bucket ID
   *
   * @apiSuccess {Object[]} data Entry data
   * @apiSuccess {UUID} data.id Entry ID
   * @apiSuccess {String} data.filesize Filesize in nearest byte unit
   * @apiSuccess {String='4K 2160p','FHD 1080p','HD 720p','HQ 480p','LQ 360p'} data.quality Video quality
   * @apiSuccess {String} data.title Entry title
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     {
   *       data: [
   *         {
   *           "id": "9ef81943-78f0-4d1c-a831-a59fb5af339c"
   *           "filesize": "10.25 GB",
   *           "quality": "FHD 1080p",
   *           "title": "Title",
   *         }, { ... }
   *       ],
   *       stats: {
   *         from: "a",
   *         to: "d",
   *       }
   *     }
   *
   *
   * @apiError Unauthorized There is no login token provided, or the login token provided is invalid
   * @apiError Invalid The provided ID is invalid, or the item does not exist
   *
   * @apiErrorExample Invalid
   *     HTTP/1.1 401 Forbidden
   *     {
   *       "status": "401",
   *       "message": "Bucket ID does not exist"
   *     }
   */
  public function get($id): JsonResponse {
    try {
      return response()->json([
        'data' => $this->entryRepository->getByBucket($id),
      ]);
    } catch (ModelNotFoundException) {
      return response()->json([
        'status' => 401,
        'message' => 'Bucket ID does not exist',
      ], 401);
    }
  }
}
