<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Repositories\EntryRepository;
use App\Requests\Entry\AddRequest;
use App\Requests\Entry\EditRequest;
use App\Resources\Entry\EntryResource;
use App\Resources\Entry\EntryCollection;

class EntryController extends Controller {

  private EntryRepository $entryRepository;

  public function __construct(EntryRepository $entryRepository) {
    $this->entryRepository = $entryRepository;
  }


  /**
   * @api {get} /api/entry Retrieve Entry
   * @apiName RetrieveEntry
   * @apiGroup Entry
   *
   * @apiHeader {String} token User login token
   * @apiParam {String} [limit] Page Limit
   *
   * @apiSuccess {Object[]} data Entry Data
   * @apiSuccess {Number} data.id Entry ID
   * @apiSuccess {Date} data.dateFinsihed Date fisished or date last rewatched
   * @apiSuccess {String} data.encoder Title encoder
   * @apiSuccess {Number} data.episodes Number of episodes
   * @apiSuccess {String} data.filesize Filesize in bytes
   * @apiSuccess {Number} data.ovas Number of OVAs
   * @apiSuccess {String='4K 2160p','FHD 1080p','HD 720p','HQ 480p','LQ 360p'} data.quality Video quality
   * @apiSuccess {Number} data.rating Averaged rating of Audio, Enjoyment, Graphics and Plot
   * @apiSuccess {String} data.release Season and year in which the title was released
   * @apiSuccess {String} data.remarks Any remarks for the title
   * @apiSuccess {Number} data.rewatchLast Last rewatched date in Unix formatting
   * @apiSuccess {Number} data.specials Number of specials
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     [
   *       {
   *         "id": 1
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
   *         "title": "Title",
   *       }, { ... }
   *     ]
   *
   * @apiError Unauthorized There is no login token provided, or the login token provided is invalid
   *
   * @apiErrorExample Unauthorized
   *     HTTP/1.1 401 Forbidden
   *     {
   *       "status": "Unauthorized",
   *       "message": "Unauthorized"
   *     }
   */
  public function index(): JsonResponse {
    return response()->json([
      'data' => EntryCollection::collection($this->entryRepository->getAll()),
    ]);
  }


  /**
   * @api {get} /api/entry/:id Retrieve Specific Entry
   * @apiName RetrieveSpecificEntry
   * @apiGroup Entry
   *
   * @apiHeader {String} token User login token
   * @apiParam {Number} id Entry ID.
   *
   * @apiSuccess {Object} data Entry Data
   * @apiSuccess {Number} data.dateFinished Date Finished in Unix formatting
   * @apiSuccess {Number} data.duration Duration in seconds
   * @apiSuccess {Number} data.filesize Filesize in bytes
   * @apiSuccess {Boolean} data.inhdd Flag if title is located in HDD
   * @apiSuccess {String='4K 2160p','FHD 1080p','HD 720p','HQ 480p','LQ 360p'} data.quality Video quality
   * @apiSuccess {Object} data.rating Rating of Audio, Enjoyment, Graphics and Plot
   * @apiSuccess {Number} data.rating.audio Rating of Audio quality
   * @apiSuccess {Number} data.rating.enjoyment Rating of Enjoyment
   * @apiSuccess {Number} data.rating.graphics Rating of Graphics quality
   * @apiSuccess {Number} data.rating.plot Rating of Plot depth
   * @apiSuccess {String='Winter','Spring','Summer','Fall'} data.releaseSeason Season in which the title was released
   * @apiSuccess {String} data.releaseYear Year in which the title was released converted to String
   * @apiSuccess {String} data.rewatch Comma-separated string for rewatches in Unix formatted dates
   * @apiSuccess {Number} data.rewatchLast Last rewatched date in Unix formatting
   * @apiSuccess {String} data.variants Comma-separated title variants
   * @apiSuccess {Number=0,1,2} data.watchStatus 0 = Unwatched, 1 = Watched, 2 = Downloaded
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     {
   *       "_id": {
   *         "$oid": 1234abcd5678efgh
   *       },
   *       "dateFinished": 1546185600,
   *       "duration": 12345,
   *       "encoder": "encoder",
   *       "episodes": 25,
   *       "filesize": 123456789,
   *       "firstSeasonTitle": "First Season Title",
   *       "inhdd": true,
   *       "offquel": "Offquel1, Offquel2, Offquel3",
   *       "ovas": 1,
   *       "prequel": "Prequel Title",
   *       "quality": "FHD 1080p",
   *       "rating": {
   *           "audio": 5,
   *           "enjoyment": 7,
   *           "graphics": 4,
   *           "plot": 7
   *       },
   *       "releaseSeason": "Spring",
   *       "releaseYear": "2017",
   *       "remarks": "",
   *       "rewatch": "1553270400, 1553260400",
   *       "rewatchLast": 1553270400,
   *       "seasonNumber": 2,
   *       "sequel": "Sequel Title",
   *       "specials": 1,
   *       "title": "Title",
   *       "variants": "Variant1, Variant2",
   *       "watchStatus": 0
   *     }
   *
   * @apiError Unauthorized There is no login token provided, or the login token provided is invalid
   *
   * @apiErrorExample Unauthorized
   *     HTTP/1.1 401 Forbidden
   *     {
   *       "status": "Unauthorized",
   *       "message": "Unauthorized"
   *     }
   */
  public function get($id): JsonResponse {
    return response()->json([
      'data' => new EntryResource($this->entryRepository->get($id)),
    ]);
  }


  /**
   * @api {post} /api/entry Create Entry
   * @apiName CreateEntry
   * @apiGroup Entry
   *
   * @apiHeader {String} token User login token
   *
   * @apiBody {String} title Entry title
   * @apiBody {Number} [dateFinished] Date Finished in Unix formatting
   * @apiBody {Number} [duration] Duration in seconds
   * @apiBody {Number} [episodes] Number of episodes
   * @apiBody {Number} [filesize] Filesize in bytes
   * @apiBody {String} [firstSeasonTitle] Title of the first season in a series
   * @apiBody {Boolean} [inhdd=true] Flag if title is located in HDD
   * @apiBody {String} [offquel] Comma-separated offquel titles
   * @apiBody {Number} [ovas] Number of OVAs
   * @apiBody {String} [prequel] Title of prequel
   * @apiBody {String='4K 2160p','FHD 1080p','HD 720p','HQ 480p','LQ 360p'} [quality='FHD 1080p'] Video quality
   * @apiBody {Object} [rating] Rating of Audio, Enjoyment, Graphics and Plot
   * @apiBody {Number} [rating.audio] Rating of Audio quality
   * @apiBody {Number} [rating.enjoyment] Rating of Enjoyment
   * @apiBody {Number} [rating.graphics] Rating of Graphics quality
   * @apiBody {Number} [rating.plot] Rating of Plot depth
   * @apiBody {String='Winter','Spring','Summer','Fall'} [releaseSeason] Season in which the title was released
   * @apiBody {String} [releaseYear] Year in which the title was released converted to String
   * @apiBody {String} [remarks] Any comments or remarks
   * @apiBody {String} [rewatch] Comma-separated string for rewatches in Unix formatted dates
   * @apiBody {Number} [rewatchLast] Last rewatched date in Unix formatting
   * @apiBody {Number} [seasonNumber] Current season number of title
   * @apiBody {String} [sequel] Sequel title
   * @apiBody {Number} [specials] Number of special episodes
   * @apiBody {String} [variants] Comma-separated title variants
   * @apiBody {Number=0,1,2} [watchStatus=1] 0 = Unwatched, 1 = Watched, 2 = Downloaded
   *
   * @apiSuccess {Object} data Created Entry Data
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     {
   *       "_id": {
   *         "$oid": 1234abcd5678efgh
   *       },
   *       "dateFinished": 1546185600,
   *       "duration": 12345,
   *       "encoder": "encoder",
   *       "episodes": 25,
   *       "filesize": 123456789,
   *       "firstSeasonTitle": "First Season Title",
   *       "inhdd": true,
   *       "offquel": "Offquel1, Offquel2, Offquel3",
   *       "ovas": 1,
   *       "prequel": "Prequel Title",
   *       "quality": "FHD 1080p",
   *       "rating": {
   *           "audio": 5,
   *           "enjoyment": 7,
   *           "graphics": 4,
   *           "plot": 7
   *       },
   *       "releaseSeason": "Spring",
   *       "releaseYear": "2017",
   *       "remarks": "",
   *       "rewatch": "1553270400, 1553260400",
   *       "rewatchLast": 1553270400,
   *       "seasonNumber": 2,
   *       "sequel": "Sequel Title",
   *       "specials": 1,
   *       "title": "Title",
   *       "variants": "Variant1, Variant2",
   *       "watchStatus": 0
   *     }
   *
   * @apiError Unauthorized There is no login token provided, or the login token provided is invalid
   *
   * @apiErrorExample Unauthorized
   *     HTTP/1.1 401 Forbidden
   *     {
   *       "status": "Unauthorized",
   *       "message": "Unauthorized"
   *     }
   */
  public function add(AddRequest $request): JsonResponse {
    return response()->json([
      'data' => $this->entryRepository->add($request->all()),
    ]);
  }

  public function edit(EditRequest $request, $id): JsonResponse {
    return response()->json([
      'data' => $this->entryRepository->edit($request->all(), $id),
    ]);
  }

  public function delete($id): JsonResponse {
    try {
      return response()->json([
        'data' => $this->entryRepository->delete($id),
      ]);
    } catch (ModelNotFoundException) {
      return response()->json([
        'status' => 401,
        'message' => 'Entry ID does not exist',
      ], 401);
    }
  }
}
