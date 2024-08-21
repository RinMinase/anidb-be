<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\BucketRepository;

use App\Requests\ImportRequest;

use App\Resources\DefaultResponse;
use App\Resources\ErrorResponse;

class BucketController extends Controller {

  private BucketRepository $bucketRepository;

  public function __construct(BucketRepository $bucketRepository) {
    $this->bucketRepository = $bucketRepository;
  }

  /**
   * @OA\Post(
   *   tags={"Import"},
   *   path="/api/buckets/import",
   *   summary="Import a JSON file to seed data for buckets table",
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
      return ErrorResponse::badRequest("The file is an invalid JSON");
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

  /* Temporarily removed as API are unused */

  // public function index(): JsonResponse {
  //   return DefaultResponse::success(null, [
  //     'data' => $this->bucketRepository->getAll(),
  //   ]);
  // }

  // public function get($id): JsonResponse {
  //   return response()->json([
  //     'data' => $this->bucketRepository->get($id),
  //   ]);
  // }

  // public function add(Request $request): JsonResponse {
  //   $this->bucketRepository->add($request->all());

  //   return DefaultResponse::success();
  // }

  // public function edit(Request $request, $id): JsonResponse {
  //   $this->bucketRepository->edit(
  //     $request->except(['_method']),
  //     $id
  //   );

  //   return DefaultResponse::success();
  // }

  // public function delete($id): JsonResponse {
  //   $this->bucketRepository->delete($id);

  //   return DefaultResponse::success();
  // }
}
