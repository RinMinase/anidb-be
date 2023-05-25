<?php

namespace App\Controllers;

use Exception;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Repositories\EntryRepository;
use App\Requests\Entry\AddRequest;
use App\Requests\Entry\EditRequest;
use App\Resources\Entry\EntryResource;

class EntryController extends Controller {

  private EntryRepository $entryRepository;

  public function __construct(EntryRepository $entryRepository) {
    $this->entryRepository = $entryRepository;
  }

  /**
   * @OA\Get(
   *   tags={"Entry"},
   *   path="/api/entries",
   *   summary="Get All Entries",
   *   security={{"token":{}}},
   *   @OA\Response(
   *     response=200,
   *     description="OK",
   *     @OA\JsonContent(
   *       @OA\Property(property="data", ref="#/components/schemas/EntryCollection"),
   *       @OA\Property(
   *         property="meta",
   *         type="object",
   *         ref="#/components/schemas/Pagination",
   *       ),
   *     )
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   * )
   */
  public function index(Request $request): JsonResponse {
    return response()->json($this->entryRepository->getAll($request));
  }

  /**
   * @OA\Get(
   *   tags={"Entry"},
   *   path="/api/entries/{entry_id}",
   *   summary="Get an Entry",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="entry_id",
   *     description="Entry ID",
   *     in="path",
   *     required=true,
   *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *     @OA\Schema(type="string", format="uuid"),
   *   ),
   *
   *   @OA\Response(
   *     response=200,
   *     description="OK",
   *     @OA\JsonContent(ref="#/components/schemas/Entry"),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   * )
   */
  public function get($id): JsonResponse {
    try {
      return response()->json([
        'data' => new EntryResource($this->entryRepository->get($id)),
      ]);
    } catch (ModelNotFoundException) {
      return response()->json([
        'status' => 401,
        'message' => 'The provided ID is invalid, or the item does not exist',
      ], 401);
    }
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
   * @apiSuccess {Object} data Created Entry data
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
    try {
      $this->entryRepository->add($request);

      return response()->json([
        'status' => 200,
        'message' => 'Success',
      ]);
    } catch (Exception $e) {
      throw $e;
    }
  }

  public function edit(EditRequest $request, $uuid): JsonResponse {
    try {
      $this->entryRepository->edit($request, $uuid);

      return response()->json([
        'status' => 200,
        'message' => 'Success',
      ]);
    } catch (Exception) {
      return response()->json([
        'status' => 500,
        'message' => 'Failed',
      ], 500);
    }
  }

  /**
   * @OA\Delete(
   *   tags={"Entry"},
   *   path="/api/entries/{entry_id}",
   *   summary="Delete an Entry",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="entry_id",
   *     description="Entry ID",
   *     in="path",
   *     required=true,
   *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *     @OA\Schema(type="string", format="uuid"),
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   * )
   */
  public function delete($id): JsonResponse {
    try {
      $this->entryRepository->delete($id);

      return response()->json([
        'status' => 200,
        'message' => 'Success',
      ]);
    } catch (ModelNotFoundException) {
      return response()->json([
        'status' => 401,
        'message' => 'Entry ID does not exist',
      ], 401);
    }
  }

  public function import(Request $request): JsonResponse {
    try {
      $file = json_decode($request->file('file')->get());
      $count = $this->entryRepository->import($file);

      return response()->json([
        'status' => 200,
        'message' => 'Success',
        'data' => [
          'acceptedImports' => $count,
          'totalJsonEntries' => count($file),
        ],
      ]);
    } catch (ModelNotFoundException) {
      return response()->json([
        'status' => 401,
        'message' => 'Failed to import JSON file',
      ], 401);
    }
  }

  /**
   * @OA\Post(
   *   tags={"Entry"},
   *   path="/api/entries/img-upload/{entry_id}",
   *   summary="Upload an Image to Entry",
   *   description="POST request with '_method' in parameters, because PHP can't populate files in PUT/PATCH requests :: Ref. https://stackoverflow.com/a/65009135",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="entry_id",
   *     description="Entry ID",
   *     in="path",
   *     required=true,
   *     example="87d66263-269c-4f7c-9fb8-dd78c4408ff6",
   *     @OA\Schema(type="string", format="uuid"),
   *   ),
   *
   *   @OA\RequestBody(
   *     required=true,
   *     @OA\MediaType(
   *       mediaType="multipart/form-data",
   *       @OA\Schema(
   *         type="object",
   *         @OA\Property(property="_method", type="string", example="PUT"),
   *         @OA\Property(property="image", type="string", format="binary"),
   *       ),
   *     ),
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   * )
   */
  public function imageUpload(Request $request, $uuid): JsonResponse {
    try {
      $this->entryRepository->upload($request, $uuid);

      return response()->json([
        'status' => 200,
        'message' => 'Success',
      ]);
    } catch (ModelNotFoundException) {
      return response()->json([
        'status' => 401,
        'message' => 'Entry ID does not exist',
      ], 401);
    }
  }

  /**
   * @OA\Put(
   *   tags={"Entry"},
   *   path="/api/entries/ratings/{entry_id}",
   *   summary="Edit Entry Ratings",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="entry_id",
   *     description="Entry ID",
   *     in="path",
   *     required=true,
   *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *     @OA\Schema(type="string", format="uuid"),
   *   ),
   *   @OA\Parameter(
   *     name="audio",
   *     in="query",
   *     example=10,
   *     @OA\Schema(type="integer", format="int32", minimum=1, maximum=10),
   *   ),
   *   @OA\Parameter(
   *     name="enjoyment",
   *     in="query",
   *     example=10,
   *     @OA\Schema(type="integer", format="int32", minimum=1, maximum=10),
   *   ),
   *   @OA\Parameter(
   *     name="graphics",
   *     in="query",
   *     example=10,
   *     @OA\Schema(type="integer", format="int32", minimum=1, maximum=10),
   *   ),
   *   @OA\Parameter(
   *     name="plot",
   *     in="query",
   *     example=10,
   *     @OA\Schema(type="integer", format="int32", minimum=1, maximum=10),
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   * )
   */
  public function ratings(Request $request, $uuid): JsonResponse {
    try {
      $this->entryRepository->ratings($request, $uuid);

      return response()->json([
        'status' => 200,
        'message' => 'Success',
      ]);
    } catch (ModelNotFoundException) {
      return response()->json([
        'status' => 401,
        'message' => 'Entry ID does not exist',
      ], 401);
    }
  }

  /**
   * @OA\Post(
   *   tags={"Entry"},
   *   path="/api/entries/rewatch/{entry_id}",
   *   summary="Add an Entry Rewatch",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="entry_id",
   *     description="Entry ID",
   *     in="path",
   *     required=true,
   *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *     @OA\Schema(type="string", format="uuid"),
   *   ),
   *   @OA\Parameter(
   *     name="date_rewatched",
   *     in="query",
   *     example="2022-01-23",
   *     @OA\Schema(type="string", format="date"),
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   * )
   */
  public function rewatchAdd(Request $request, $uuid): JsonResponse {
    try {
      $this->entryRepository->rewatchAdd($request, $uuid);

      return response()->json([
        'status' => 200,
        'message' => 'Success',
      ]);
    } catch (ModelNotFoundException) {
      return response()->json([
        'status' => 401,
        'message' => 'Entry ID does not exist',
      ], 401);
    }
  }

  /**
   * @OA\Delete(
   *   tags={"Entry"},
   *   path="/api/entries/rewatch/{entry_rewatch_id}",
   *   summary="Delete an Entry Rewatch",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="entry_rewatch_id",
   *     description="Entry Rewatch ID",
   *     in="path",
   *     required=true,
   *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *     @OA\Schema(type="string", format="uuid"),
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   * )
   */
  public function rewatchDelete($uuid): JsonResponse {
    try {
      $this->entryRepository->rewatchDelete($uuid);

      return response()->json([
        'status' => 200,
        'message' => 'Success',
      ]);
    } catch (ModelNotFoundException) {
      return response()->json([
        'status' => 401,
        'message' => 'Entry ID does not exist',
      ], 401);
    }
  }

  /**
   * @OA\Get(
   *   tags={"Entry"},
   *   path="/api/entries/titles",
   *   summary="Search Entry titles - For First Season Title, Prequel and Sequel",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="id",
   *     description="Entry ID, search should not include this entry",
   *     in="query",
   *     required=true,
   *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *     @OA\Schema(type="string", format="uuid"),
   *   ),
   *    @OA\Parameter(
   *     name="needle",
   *     description="Search query",
   *     in="query",
   *     required=true,
   *     example="title",
   *     @OA\Schema(type="string"),
   *   ),
   *
   *   @OA\Response(
   *     response=200,
   *     description="OK",
   *     @OA\JsonContent(
   *       example={
   *         "data": {
   *           "title 1",
   *           "title 2",
   *           "title 3",
   *         },
   *       },
   *       @OA\Property(property="data", type="array", @OA\Items(type="string")),
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   * )
   */
  public function getTitles(Request $request): JsonResponse {
    return response()->json([
      'data' => $this->entryRepository->getTitles(
        $request->get('id'),
        $request->get('needle'),
      ),
    ]);
  }
}
