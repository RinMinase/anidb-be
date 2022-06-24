<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

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
   * @apiParam {String} [needle] Search keyword / query
   * @apiParam {String} [haystack=title] Column used for the searching the needle
   * @apiParam {String} [column] Page Limit
   * @apiParam {String} [order] Page Limit
   * @apiParam {String} [limit] Page Limit
   * @apiParam {String} [page] Page number
   *
   * @apiSuccess {Object[]} data Entry Data
   * @apiSuccess {UUID} data.id Entry ID
   * @apiSuccess {Date} data.dateFinsihed Date fisished or date last rewatched
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
  public function index(Request $request): JsonResponse {
    return response()->json([
      'data' => EntryCollection::collection($this->entryRepository->getAll($request)),
    ]);
  }


  /**
   * @api {get} /api/entry/:id Retrieve Specific Entry
   * @apiName RetrieveSpecificEntry
   * @apiGroup Entry
   *
   * @apiHeader {String} token User login token
   * @apiParam {UUID} id Entry UUID.
   *
   * @apiSuccess {Object} data Entry Data
   * @apiSuccess {UUID} data.id Entry ID
   * @apiSuccess {Date} data.dateInitFinished Intial date finished
   * @apiSuccess {Date} data.dateLastFinished Last rewatch date
   * @apiSuccess {String} data.duration Duration in xx hours xx minutes xx seconds
   * @apiSuccess {String} data.encoder Combined encoder value
   * @apiSuccess {String} data.encoderAudio Audio encoder
   * @apiSuccess {String} data.encoderSubs Subs encoder
   * @apiSuccess {String} data.encoderVideo Video encoder
   * @apiSuccess {Number} data.episodes Number of episodes
   * @apiSuccess {String} data.filesize Filesize in nearest byte unit
   * @apiSuccess {Object[]} offquels List of offquel titles
   * @apiSuccess {Number} data.offquels.id Offquel title id
   * @apiSuccess {String} data.offquels.title Offquel title name
   * @apiSuccess {Number} data.ovas Number of OVAs
   * @apiSuccess {String} data.prequel Prequel title
   * @apiSuccess {String='4K 2160p','FHD 1080p','HD 720p','HQ 480p','LQ 360p'} data.quality Video quality
   * @apiSuccess {String[]} rewatches List of rewatch dates
   * @apiSuccess {Number} data.seasonNumber nth season from first title in series
   * @apiSuccess {String} data.seasonFirstTitle 1st season title in series
   * @apiSuccess {String} data.sequel Sequel title
   * @apiSuccess {Number} data.specials Number of specials
   * @apiSuccess {String} data.variants Comma separated title variants
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     {
   *       "id": "9ef81943-78f0-4d1c-a831-a59fb5af339c"
   *       "dateInitFinished": "Jan 01, 2001",
   *       "dateLastFinished": "Mar 01, 2011",
   *       "duration": 12 hours 34 minutes 56 seconds,
   *       "encoder": "encVideo—encAudio—encSubs",
   *       "encoderAudio": "encAudio",
   *       "encoderSubs": "encVideo",
   *       "encoderVideo": "encSubs",
   *       "episodes": 25,
   *       "filesize": "10.25 GB",
   *       "offquels" [
   *         {
   *           "id": 3,
   *           "title": "Offquel Title",
   *         }, {...}
   *       ]
   *       "ovas": 1,
   *       "prequel": "Prequel Title",
   *       "quality": "FHD 1080p",
   *       "rewatches": [
   *         "Feb 10, 2011",
   *         "Mar 01, 2011"
   *       ]
   *       "seasonNumber": 2,
   *       "seasonFirstTitle": "First Title",
   *       "sequel": "Sequel Title",
   *       "specials": 1,
   *       "variants": "ShortTitle"
   *     }
   *
   * @apiError Unauthorized There is no login token provided, or the login token provided is invalid
   * @apiError Invalid The provided ID is invalid, or the item does not exist
   *
   * @apiErrorExample Invalid
   *     HTTP/1.1 401 Forbidden
   *     {
   *       "status": "401",
   *       "message": "The provided ID is invalid, or the item does not exist"
   *     }
   */
  public function get($id): JsonResponse {
    try {
      return response()->json([
        'data' => new EntryResource($this->entryRepository->get($id)),
      ]);
    } catch (QueryException) {
      return response()->json([
        'status' => 401,
        'message' => 'The provided ID is invalid, or the item does not exist',
      ], 401);
    }
  }


  public function getLast(): JsonResponse {
    return response()->json([
      'data' => EntryCollection::collection($this->entryRepository->getLast()),
    ]);
  }

  public function getByName(): JsonResponse {
    return response()->json([
      'data' => EntryCollection::collection($this->entryRepository->getByName()),
    ]);
  }

  public function getByLetter($letter): JsonResponse {
    return response()->json([
      'data' => EntryCollection::collection($this->entryRepository->getByLetter($letter)),
    ]);
  }

  public function getByYear(): JsonResponse {
    return response()->json([
      'data' => EntryCollection::collection($this->entryRepository->getByYear()),
    ]);
  }

  public function getBySeason($year): JsonResponse {
    return response()->json([
      'data' => EntryCollection::collection($this->entryRepository->getBySeason($year)),
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
