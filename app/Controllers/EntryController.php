<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\EntryRepository;

use App\Requests\Entry\AddEditRequest;
use App\Requests\Entry\AddRewatchRequest;
use App\Requests\Entry\ImageUploadRequest;
use App\Requests\Entry\ImportRequest;
use App\Requests\Entry\RatingsRequest;
use App\Requests\Entry\SearchRequest;
use App\Requests\Entry\SearchTitlesRequest;

use App\Resources\Entry\EntryResource;
use App\Resources\DefaultResponse;

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
   *   @OA\Parameter(ref="#/components/parameters/entry_search_needle"),
   *   @OA\Parameter(ref="#/components/parameters/entry_search_haystack"),
   *   @OA\Parameter(ref="#/components/parameters/entry_search_column"),
   *   @OA\Parameter(ref="#/components/parameters/entry_search_order"),
   *   @OA\Parameter(ref="#/components/parameters/entry_search_page"),
   *   @OA\Parameter(ref="#/components/parameters/entry_search_limit"),
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
  public function index(SearchRequest $request): JsonResponse {
    return response()->json(
      $this->entryRepository->getAll(
        $request->only('needle', 'haystack', 'column', 'order', 'limit', 'page')
      )
    );
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
    return response()->json([
      'data' => new EntryResource($this->entryRepository->get($id)),
    ]);
  }

  /**
   * @OA\Post(
   *   tags={"Entry"},
   *   path="/api/entries",
   *   summary="Add an Entry",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_id_quality"),
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_title"),
   *
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_date_finished"),
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_duration"),
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_filesize"),
   *
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_episodes"),
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_ovas"),
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_specials"),
   *
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_season_number"),
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_season_first_title_id"),
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_prequel_id"),
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_sequel_id"),
   *
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_encoder_video"),
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_encoder_audio"),
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_encoder_subs"),
   *
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_encoder_audio"),
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_encoder_subs"),
   *
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_release_year"),
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_release_season"),
   *
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_variants"),
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_remarks"),
   *
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_id_codec_audio"),
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_id_codec_video"),
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_codec_hdr"),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function add(AddEditRequest $request): JsonResponse {
    $this->entryRepository->add(
      $request->only(
        'id_quality',
        'title',
        'date_finished',
        'duration',
        'filesize',
        'episodes',
        'ovas',
        'specials',
        'season_number',
        'season_first_title_id',
        'prequel_id',
        'sequel_id',
        'encoder_video',
        'encoder_audio',
        'encoder_subs',
        'release_year',
        'release_season',
        'variants',
        'remarks',
        'id_codec_audio',
        'id_codec_video',
        'id_codec_video',
        'codec_hdr',
      )
    );

    return DefaultResponse::success();
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
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_id_quality"),
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_title"),
   *
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_date_finished"),
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_duration"),
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_filesize"),
   *
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_episodes"),
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_ovas"),
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_specials"),
   *
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_season_number"),
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_season_first_title_id"),
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_prequel_id"),
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_sequel_id"),
   *
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_encoder_video"),
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_encoder_audio"),
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_encoder_subs"),
   *
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_encoder_audio"),
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_encoder_subs"),
   *
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_release_year"),
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_release_season"),
   *
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_variants"),
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_remarks"),
   *
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_id_codec_audio"),
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_id_codec_video"),
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_codec_hdr"),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function edit(AddEditRequest $request, $uuid): JsonResponse {
    $this->entryRepository->edit(
      $request->only(
        'id_quality',
        'title',
        'date_finished',
        'duration',
        'filesize',
        'episodes',
        'ovas',
        'specials',
        'season_number',
        'season_first_title_id',
        'prequel_id',
        'sequel_id',
        'encoder_video',
        'encoder_audio',
        'encoder_subs',
        'release_year',
        'release_season',
        'variants',
        'remarks',
        'id_codec_audio',
        'id_codec_video',
        'id_codec_video',
        'codec_hdr',
      ),
      $uuid,
    );

    return DefaultResponse::success();
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
    $this->entryRepository->delete($id);

    return DefaultResponse::success();
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
  public function import(ImportRequest $request): JsonResponse {
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
  public function imageUpload(ImageUploadRequest $request, $uuid): JsonResponse {
    $this->entryRepository->upload($request->file('image')->getRealPath(), $uuid);

    return DefaultResponse::success();
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
   *   @OA\Parameter(ref="#/components/parameters/entry_ratings_audio"),
   *   @OA\Parameter(ref="#/components/parameters/entry_ratings_enjoyment"),
   *   @OA\Parameter(ref="#/components/parameters/entry_ratings_graphics"),
   *   @OA\Parameter(ref="#/components/parameters/entry_ratings_plot"),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function ratings(RatingsRequest $request, $uuid): JsonResponse {
    $this->entryRepository->ratings(
      $request->only('audio', 'enjoyment', 'graphics', 'plot'),
      $uuid,
    );

    return DefaultResponse::success();
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
   *   @OA\Parameter(ref="#/components/parameters/entry_add_rewatch_date_rewatched"),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function rewatchAdd(AddRewatchRequest $request, $uuid): JsonResponse {
    $this->entryRepository->rewatchAdd($request->only('date_rewatched'), $uuid);

    return DefaultResponse::success();
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
    $this->entryRepository->rewatchDelete($uuid);

    return DefaultResponse::success();
  }

  /**
   * @OA\Get(
   *   tags={"Entry"},
   *   path="/api/entries/titles",
   *   summary="Search Entry titles - For First Season Title, Prequel and Sequel",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(ref="#/components/parameters/entry_search_titles_id"),
   *   @OA\Parameter(ref="#/components/parameters/entry_search_titles_needle"),
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
  public function getTitles(SearchTitlesRequest $request): JsonResponse {
    return response()->json([
      'data' => $this->entryRepository->getTitles(
        $request->get('id'),
        $request->get('needle') ?? '',
      ),
    ]);
  }
}
