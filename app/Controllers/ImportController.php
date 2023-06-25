<?php

namespace App\Controllers;


use Illuminate\Http\JsonResponse;

use App\Repositories\ImportRepository;

use App\Requests\Import\ImportRequest;

use App\Resources\DefaultResponse;

class ImportController extends Controller {

  private ImportRepository $importRepository;

  public function __construct(ImportRepository $importRepository) {
    $this->importRepository = $importRepository;
  }

  /**
   * @OA\Schema(
   *   schema="ImportDataCount",
   *   title="Import Accepted & Total Schema",
   *   @OA\Property(property="accepted", type="integer", format="int32", example=0),
   *   @OA\Property(property="total", type="integer", format="int32", example=0),
   * ),
   * @OA\Post(
   *   tags={"Import"},
   *   path="/api/import",
   *   summary="Import a JSON file to seed data for all tables",
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
   *       allOf={
   *         @OA\Schema(ref="#/components/schemas/DefaultSuccess"),
   *         @OA\Schema(
   *           @OA\Property(
   *             property="data",
   *             @OA\Property(property="entries", ref="#/components/schemas/ImportDataCount"),
   *             @OA\Property(property="buckets", ref="#/components/schemas/ImportDataCount"),
   *             @OA\Property(property="sequences", ref="#/components/schemas/ImportDataCount"),
   *             @OA\Property(property="groups", ref="#/components/schemas/ImportDataCount"),
   *           ),
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function index(ImportRequest $request): JsonResponse {
    $file = json_decode($request->file('file')->get());
    $import_count = $this->importRepository->import($file);

    $entry_count = 0;
    if (!empty($file->entry)) {
      $entry_count = count($file->entry);
    }

    $bucket_count = 0;
    if (!empty($file->bucket)) {
      $bucket_count = count($file->bucket);
    }

    $sequence_count = 0;
    if (!empty($file->sequence)) {
      $sequence_count = count($file->sequence);
    }

    $group_count = 0;
    if (!empty($file->group)) {
      $group_count = count($file->group);
    }

    return DefaultResponse::success(null, [
      'data' => [
        'entries' => [
          'accepted' => $import_count['entry'],
          'total' => $entry_count,
        ],
        'buckets' => [
          'accepted' => $import_count['bucket'],
          'total' => $bucket_count,
        ],
        'sequences' => [
          'accepted' => $import_count['sequence'],
          'total' => $sequence_count,
        ],
        'groups' => [
          'accepted' => $import_count['group'],
          'total' => $group_count,
        ],
      ],
    ]);
  }
}
