<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Exceptions\JsonParsingException;
use App\Repositories\BucketRepository;
use App\Requests\ImportRequest;
use App\Resources\DefaultResponse;

class BucketController extends Controller {

  private BucketRepository $bucketRepository;

  public function __construct(BucketRepository $bucketRepository) {
    $this->bucketRepository = $bucketRepository;
  }

  /**
   * @OA\Post(
   *   tags={"Import - Archaic"},
   *   path="/api/archaic/import/buckets",
   *   summary="Import a JSON file to seed data for buckets table",
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
    $count = $this->bucketRepository->import($data);

    return DefaultResponse::success(null, [
      'data' => [
        'acceptedImports' => $count,
        'totalJsonEntries' => count($data),
      ],
    ]);
  }
}
