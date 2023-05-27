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
   *
   *   @OA\Parameter(
   *     name="needle",
   *     description="Search - Item to search for in haystack (column)",
   *     in="query",
   *     example="item name",
   *     @OA\Schema(type="string"),
   *   ),
   *   @OA\Parameter(
   *     name="haystack",
   *     description="Search - Column to search for",
   *     in="query",
   *     example="title",
   *     @OA\Schema(type="string", default="title"),
   *   ),
   *   @OA\Parameter(
   *     name="column",
   *     description="Order - Column to order",
   *     in="query",
   *     example="id_quality",
   *     @OA\Schema(type="string", default="id_quality"),
   *   ),
   *   @OA\Parameter(
   *     name="order",
   *     description="Order - Order the column by",
   *     in="query",
   *     @OA\Schema(type="string", default="asc", enum={"asc", "desc"}),
   *   ),
   *   @OA\Parameter(
   *     name="page",
   *     description="Pagination - Page to query",
   *     in="query",
   *     example=1,
   *     @OA\Schema(type="integer", format="int32", default=1, minimum=1),
   *   ),
   *   @OA\Parameter(
   *     name="limit",
   *     description="Pagination - Page item limit",
   *     in="query",
   *     example=1,
   *     @OA\Schema(type="integer", format="int32", default=30, minimum=1),
   *   ),
   *
   *   @OA\Response(
   *     response=200,
   *     description="OK",
   *     @OA\JsonContent(
   *       @OA\Property(property="data", ref="#/components/schemas/EntryCollection"),
   *       @OA\Property(property="meta", ref="#/components/schemas/Pagination"),
   *     )
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
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
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
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
   * @OA\Post(
   *   tags={"Entry"},
   *   path="/api/entries",
   *   summary="Add an Entry",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(ref="#/components/parameters/entry_add_id_quality"),
   *   @OA\Parameter(ref="#/components/parameters/entry_add_title"),
   *
   *   @OA\Parameter(name="date_finished", in="query", @OA\Schema(type="string", format="date")),
   *   @OA\Parameter(name="duration", in="query", @OA\Schema(type="integer", format="int64")),
   *   @OA\Parameter(name="filesize", in="query", @OA\Schema(type="integer", format="int64")),
   *
   *   @OA\Parameter(name="episodes", in="query", @OA\Schema(type="integer", format="int32")),
   *   @OA\Parameter(name="ovas", in="query", @OA\Schema(type="integer", format="int32")),
   *   @OA\Parameter(name="specials", in="query", @OA\Schema(type="integer", format="int32")),
   *
   *   @OA\Parameter(
   *     name="season_number",
   *     in="query",
   *     @OA\Schema(type="integer", format="int32")
   *   ),
   *   @OA\Parameter(
   *     name="season_first_title_id",
   *     in="query",
   *     @OA\Schema(type="string", format="uuid")
   *   ),
   *   @OA\Parameter(name="prequel_id", in="query", @OA\Schema(type="string", format="uuid")),
   *   @OA\Parameter(name="sequel_id", in="query", @OA\Schema(type="string", format="uuid")),
   *
   *   @OA\Parameter(name="encoder_video", in="query", @OA\Schema(type="string")),
   *   @OA\Parameter(name="encoder_audio", in="query", @OA\Schema(type="string")),
   *   @OA\Parameter(name="encoder_subs", in="query", @OA\Schema(type="string")),
   *
   *   @OA\Parameter(
   *     name="release_year",
   *     in="query",
   *     @OA\Schema(ref="#/components/schemas/YearSchema"),
   *   ),
   *   @OA\Parameter(
   *     name="release_season",
   *     in="query",
   *     @OA\Schema(type="string", enum={"Winter", "Spring", "Summer", "Fall"})
   *   ),
   *
   *   @OA\Parameter(name="variants", in="query", @OA\Schema(type="string")),
   *   @OA\Parameter(name="remarks", in="query", @OA\Schema(type="string")),
   *   @OA\Parameter(
   *     name="id_codec_audio",
   *     in="query",
   *     @OA\Schema(type="integer", format="int32")
   *   ),
   *   @OA\Parameter(
   *     name="id_codec_video",
   *     in="query",
   *     @OA\Schema(type="integer", format="int32")
   *   ),
   *   @OA\Parameter(
   *     name="codec_hdr",
   *     in="query",
   *     @OA\Schema(type="integer", format="int32", minimum=0, maximum=1)
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
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

  /**
   * @OA\Put(
   *   tags={"Entry"},
   *   path="/api/entries/{entry_id}",
   *   summary="Edit an Entry",
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
   *   @OA\Parameter(ref="#/components/parameters/entry_edit_id_quality"),
   *   @OA\Parameter(ref="#/components/parameters/entry_edit_title"),
   *
   *   @OA\Parameter(name="date_finished", in="query", @OA\Schema(type="string", format="date")),
   *   @OA\Parameter(name="duration", in="query", @OA\Schema(type="integer", format="int64")),
   *   @OA\Parameter(name="filesize", in="query", @OA\Schema(type="integer", format="int64")),
   *
   *   @OA\Parameter(name="episodes", in="query", @OA\Schema(type="integer", format="int32")),
   *   @OA\Parameter(name="ovas", in="query", @OA\Schema(type="integer", format="int32")),
   *   @OA\Parameter(name="specials", in="query", @OA\Schema(type="integer", format="int32")),
   *
   *   @OA\Parameter(
   *     name="season_number",
   *     in="query",
   *     @OA\Schema(type="integer", format="int32")
   *   ),
   *   @OA\Parameter(
   *     name="season_first_title_id",
   *     in="query",
   *     @OA\Schema(type="string", format="uuid")
   *   ),
   *   @OA\Parameter(name="prequel_id", in="query", @OA\Schema(type="string", format="uuid")),
   *   @OA\Parameter(name="sequel_id", in="query", @OA\Schema(type="string", format="uuid")),
   *
   *   @OA\Parameter(name="encoder_video", in="query", @OA\Schema(type="string")),
   *   @OA\Parameter(name="encoder_audio", in="query", @OA\Schema(type="string")),
   *   @OA\Parameter(name="encoder_subs", in="query", @OA\Schema(type="string")),
   *
   *   @OA\Parameter(
   *     name="release_year",
   *     in="query",
   *     @OA\Schema(ref="#/components/schemas/YearSchema"),
   *   ),
   *   @OA\Parameter(
   *     name="release_season",
   *     in="query",
   *     @OA\Schema(type="string", enum={"Winter", "Spring", "Summer", "Fall"})
   *   ),
   *
   *   @OA\Parameter(name="variants", in="query", @OA\Schema(type="string")),
   *   @OA\Parameter(name="remarks", in="query", @OA\Schema(type="string")),
   *   @OA\Parameter(
   *     name="id_codec_audio",
   *     in="query",
   *     @OA\Schema(type="integer", format="int32")
   *   ),
   *   @OA\Parameter(
   *     name="id_codec_video",
   *     in="query",
   *     @OA\Schema(type="integer", format="int32")
   *   ),
   *   @OA\Parameter(
   *     name="codec_hdr",
   *     in="query",
   *     @OA\Schema(type="integer", format="int32", minimum=0, maximum=1)
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
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
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
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

  /**
   * @OA\Post(
   *   tags={"Import"},
   *   path="/api/entries/import",
   *   summary="Import a JSON file to seed data for entries table",
   *   security={{"token":{}}},
   *
   *   @OA\RequestBody(
   *     required=true,
   *     @OA\MediaType(
   *       mediaType="multipart/form-data",
   *       @OA\Schema(
   *         type="object",
   *         @OA\Property(property="file", type="string", format="binary"),
   *       ),
   *     ),
   *   ),
   *
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       example={
   *         "status": 200,
   *         "message": "Success",
   *         "data": {
   *           "acceptedImports": 0,
   *           "totalJsonEntries": 0,
   *         },
   *       },
   *       @OA\Property(property="status", type="integer", format="int32"),
   *       @OA\Property(property="message", type="integer", format="int32"),
   *       @OA\Property(
   *         property="data",
   *         @OA\Property(property="acceptedImports", type="integer", format="int32"),
   *         @OA\Property(property="totalJsonEntries", type="integer", format="int32"),
   *       ),
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
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
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
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
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
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
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
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
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
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
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
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
