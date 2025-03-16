<?php

namespace App\Controllers;

use TypeError;
use Illuminate\Http\JsonResponse;

use App\Exceptions\JsonParsingException;
use App\Exceptions\Entry\ParsingException;

use App\Repositories\EntryRepository;
use App\Repositories\EntrySearchRepository;

use App\Requests\ImportRequest;
use App\Requests\Entry\AddEditRequest;
use App\Requests\Entry\AddRewatchRequest;
use App\Requests\Entry\GetAllRequest;
use App\Requests\Entry\ImageUploadRequest;
use App\Requests\Entry\OffquelsRequest;
use App\Requests\Entry\RatingsRequest;
use App\Requests\Entry\SearchRequest;
use App\Requests\Entry\SearchTitlesRequest;

use App\Resources\Entry\EntryResource;
use App\Resources\DefaultResponse;

class EntryController extends Controller {

  private EntryRepository $entryRepository;
  private EntrySearchRepository $entrySearchRepository;

  public function __construct(
    EntryRepository $entryRepository,
    EntrySearchRepository $entrySearchRepository
  ) {
    $this->entryRepository = $entryRepository;
    $this->entrySearchRepository = $entrySearchRepository;
  }

  /**
   * @OA\Get(
   *   tags={"Entry"},
   *   path="/api/entries",
   *   summary="Get All Entries",
   *   security={{"token":{}, "api-key": {}}},
   *
   *   @OA\Parameter(ref="#/components/parameters/entry_get_all_query"),
   *   @OA\Parameter(ref="#/components/parameters/entry_get_all_column"),
   *   @OA\Parameter(ref="#/components/parameters/entry_get_all_order"),
   *   @OA\Parameter(ref="#/components/parameters/entry_get_all_page"),
   *   @OA\Parameter(ref="#/components/parameters/entry_get_all_limit"),
   *
   *   @OA\Response(
   *     response=200,
   *     description="OK",
   *     @OA\JsonContent(
   *       allOf={
   *         @OA\Schema(ref="#/components/schemas/DefaultSuccess"),
   *         @OA\Schema(ref="#/components/schemas/Pagination"),
   *         @OA\Schema(
   *           @OA\Property(
   *             property="data",
   *             type="array",
   *             @OA\Items(ref="#/components/schemas/EntrySummaryResource"),
   *           ),
   *         ),
   *       },
   *     )
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function index(GetAllRequest $request): JsonResponse {
    $data = $this->entryRepository->get_all(
      $request->only('query', 'column', 'order', 'limit', 'page')
    );

    return DefaultResponse::success(null, [
      'data' => $data['data'],
      'meta' => $data['meta'],
    ]);
  }

  /**
   * @OA\Get(
   *   tags={"Entry"},
   *   path="/api/entries/{entry_id}",
   *   summary="Get an Entry",
   *   security={{"token":{}, "api-key": {}}},
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
   *     @OA\JsonContent(
   *       allOf={
   *         @OA\Schema(ref="#/components/schemas/DefaultSuccess"),
   *         @OA\Schema(
   *           @OA\Property(property="data", ref="#/components/schemas/EntryResource"),
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function get($id): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => new EntryResource($this->entryRepository->get($id)),
    ]);
  }

  /**
   * @OA\Post(
   *   tags={"Entry"},
   *   path="/api/entries",
   *   summary="Add an Entry",
   *   security={{"token":{}, "api-key": {}}},
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
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_genres"),
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_id_watcher"),
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
        'genres',
        'id_watcher',
      )
    );

    return DefaultResponse::success();
  }

  /**
   * @OA\Put(
   *   tags={"Entry"},
   *   path="/api/entries/{entry_id}",
   *   summary="Edit an Entry",
   *   security={{"token":{}, "api-key": {}}},
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
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_genres"),
   *   @OA\Parameter(ref="#/components/parameters/entry_add_edit_id_watcher"),
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
        'genres',
        'id_watcher',
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
   *   security={{"token":{}, "api-key": {}}},
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
   * @OA\Get(
   *   tags={"Entry"},
   *   path="/api/entries/search",
   *   summary="Search All Entries",
   *   security={{"token":{}, "api-key": {}}},
   *
   *   @OA\Parameter(ref="#/components/parameters/entry_search_quality"),
   *   @OA\Parameter(ref="#/components/parameters/entry_search_title"),
   *   @OA\Parameter(ref="#/components/parameters/entry_search_date"),
   *   @OA\Parameter(ref="#/components/parameters/entry_search_filesize"),
   *   @OA\Parameter(ref="#/components/parameters/entry_search_episodes"),
   *   @OA\Parameter(ref="#/components/parameters/entry_search_ovas"),
   *   @OA\Parameter(ref="#/components/parameters/entry_search_specials"),
   *   @OA\Parameter(ref="#/components/parameters/entry_search_encoder"),
   *   @OA\Parameter(ref="#/components/parameters/entry_search_encoder_video"),
   *   @OA\Parameter(ref="#/components/parameters/entry_search_encoder_audio"),
   *   @OA\Parameter(ref="#/components/parameters/entry_search_encoder_subs"),
   *   @OA\Parameter(ref="#/components/parameters/entry_search_release"),
   *   @OA\Parameter(ref="#/components/parameters/entry_search_rating"),
   *   @OA\Parameter(ref="#/components/parameters/entry_search_remarks"),
   *   @OA\Parameter(ref="#/components/parameters/entry_search_has_remarks"),
   *   @OA\Parameter(ref="#/components/parameters/entry_search_has_image"),
   *   @OA\Parameter(ref="#/components/parameters/entry_search_is_hdr"),
   *   @OA\Parameter(ref="#/components/parameters/entry_search_codec_video"),
   *   @OA\Parameter(ref="#/components/parameters/entry_search_codec_audio"),
   *   @OA\Parameter(ref="#/components/parameters/entry_search_genres"),
   *   @OA\Parameter(ref="#/components/parameters/entry_search_watcher"),
   *   @OA\Parameter(ref="#/components/parameters/entry_search_column"),
   *   @OA\Parameter(ref="#/components/parameters/entry_search_order"),
   *
   *   @OA\Response(
   *     response=200,
   *     description="OK",
   *     @OA\JsonContent(
   *       allOf={
   *         @OA\Schema(ref="#/components/schemas/DefaultSuccess"),
   *         @OA\Schema(
   *           @OA\Property(
   *             property="data",
   *             type="array",
   *             @OA\Items(ref="#/components/schemas/EntrySummaryResource"),
   *           ),
   *           @OA\Property(
   *             property="stats",
   *             @OA\Property(property="totalFiltered", type="integer", format="int64", example=1),
   *             @OA\Property(property="totalEntries", type="integer", format="int64", example=42),
   *           ),
   *         ),
   *       },
   *     )
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function search(SearchRequest $request): JsonResponse {
    $data = $this->entrySearchRepository->search(
      $request->only(
        'quality',
        'title',
        'date',
        'filesize',
        'episodes',
        'ovas',
        'specials',
        'encoder',
        'encoder_video',
        'encoder_audio',
        'encoder_subs',
        'release',
        'remarks',
        'rating',
        'has_remarks',
        'has_image',
        'is_hdr',
        'codec_video',
        'codec_audio',
        'genres',
        'watcher',
        'column',
        'order',
      )
    );

    return DefaultResponse::success(null, [
      'data' => $data['data'],
      'stats' => $data['stats'],
    ]);
  }

  /**
   * @OA\Post(
   *   tags={"Import - Archaic"},
   *   path="/api/archaic/import/entries",
   *   summary="Import a JSON file to seed data for entries table",
   *   security={{"token":{}, "api-key": {}}},
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
   *       allOf={
   *         @OA\Schema(ref="#/components/schemas/DefaultSuccess"),
   *         @OA\Schema(
   *           @OA\Property(property="data", ref="#/components/schemas/DefaultImportSchema"),
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function import(ImportRequest $request): JsonResponse {
    $file = $request->file('file')->get();

    if (!is_json($file)) {
      throw new JsonParsingException();
    }

    $data = json_decode($file);
    $count = $this->entryRepository->import($data);

    return DefaultResponse::success(null, [
      'data' => [
        'acceptedImports' => $count,
        'totalJsonEntries' => count($data),
      ],
    ]);
  }

  /**
   * @OA\Post(
   *   tags={"Entry"},
   *   path="/api/entries/{entry_id}/offquel/{entry_offquel_id}",
   *   summary="Add Entry Offquel",
   *   security={{"token":{}, "api-key": {}}},
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
   *     name="entry_offquel_id",
   *     description="Entry Offquel ID",
   *     in="path",
   *     required=true,
   *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *     @OA\Schema(type="string", format="uuid"),
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/EntryParsingResponse"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function add_offquel($entry_uuid, $offquel_uuid): JsonResponse {
    $this->entryRepository->add_offquel($entry_uuid, $offquel_uuid);

    return DefaultResponse::success();
  }

  /**
   * @OA\Delete(
   *   tags={"Entry"},
   *   path="/api/entries/{entry_id}/offquel/{entry_offquel_id}",
   *   summary="Delete Entry Offquel",
   *   security={{"token":{}, "api-key": {}}},
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
   *     name="entry_offquel_id",
   *     description="Entry Offquel ID",
   *     in="path",
   *     required=true,
   *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *     @OA\Schema(type="string", format="uuid"),
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/EntryParsingResponse"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function delete_offquel($entry_uuid, $offquel_uuid): JsonResponse {
    $this->entryRepository->delete_offquel($entry_uuid, $offquel_uuid);

    return DefaultResponse::success();
  }

  /**
   * @OA\Post(
   *   tags={"Entry"},
   *   path="/api/entries/img-upload/{entry_id}",
   *   summary="Upload an Image to Entry",
   *   description="POST request with '_method' in parameters, because PHP can't populate files in PUT/PATCH requests :: Ref. https://stackoverflow.com/a/65009135",
   *   security={{"token":{}, "api-key": {}}},
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
   * @OA\Delete(
   *   tags={"Entry"},
   *   path="/api/entries/img-upload/{entry_id}",
   *   summary="Delete an Image of an Entry",
   *   security={{"token":{}, "api-key": {}}},
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
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function imageDelete($uuid): JsonResponse {
    $this->entryRepository->deleteImage($uuid);

    return DefaultResponse::success();
  }

  /**
   * @OA\Put(
   *   tags={"Entry"},
   *   path="/api/entries/ratings/{entry_id}",
   *   summary="Edit Entry Ratings",
   *   security={{"token":{}, "api-key": {}}},
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
   *   security={{"token":{}, "api-key": {}}},
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
   *   security={{"token":{}, "api-key": {}}},
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
   *   security={{"token":{}, "api-key": {}}},
   *
   *   @OA\Parameter(ref="#/components/parameters/entry_search_titles_id"),
   *   @OA\Parameter(ref="#/components/parameters/entry_search_titles_id_excluded"),
   *   @OA\Parameter(ref="#/components/parameters/entry_search_titles_needle"),
   *
   *   @OA\Response(
   *     response=200,
   *     description="OK",
   *     @OA\JsonContent(
   *       allOf={
   *         @OA\Schema(ref="#/components/schemas/DefaultSuccess"),
   *         @OA\Schema(
   *           @OA\Property(
   *             property="data",
   *             type="array",
   *             @OA\Items(
   *               @OA\Property(property="id", type="string", example="e9597119-8452-4f2b-96d8-f2b1b1d2f158"),
   *               @OA\Property(property="title", type="string", example="title 1"),
   *             ),
   *           ),
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function get_titles(SearchTitlesRequest $request): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->entryRepository->get_titles(
        $request->get('id'),
        $request->get('id_excluded') ?? false,
        $request->get('needle') ?? '',
      ),
    ]);
  }

  /**
   * @OA\Get(
   *   tags={"Entry"},
   *   path="/api/entries/watchers",
   *   summary="Get list of Entry Watchers",
   *   security={{"token":{}, "api-key": {}}},
   *
   *   @OA\Response(
   *     response=200,
   *     description="OK",
   *     @OA\JsonContent(
   *       allOf={
   *         @OA\Schema(ref="#/components/schemas/DefaultSuccess"),
   *         @OA\Schema(
   *           @OA\Property(
   *             property="data",
   *             type="array",
   *             @OA\Items(
   *               @OA\Property(property="id", type="integer", format="int32", example=1),
   *               @OA\Property(property="label", type="string", example="label"),
   *             ),
   *           ),
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function get_watchers(): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->entryRepository->get_watchers(),
    ]);
  }
}
