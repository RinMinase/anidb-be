<?php

namespace App\Controllers;

use Exception;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Repositories\BucketRepository;

class BucketController extends Controller {

  private BucketRepository $bucketRepository;

  public function __construct(BucketRepository $bucketRepository) {
    $this->bucketRepository = $bucketRepository;
  }

  /**
   * @OA\Get(
   *   tags={"Bucket"},
   *   path="/api/buckets",
   *   summary="Get All Buckets",
   *   security={{"token":{}}},
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       @OA\Property(
   *         property="data",
   *         type="array",
   *         @OA\Items(ref="#/components/schemas/Bucket"),
   *       ),
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function index(): JsonResponse {
    return response()->json([
      'data' => $this->bucketRepository->getAll(),
    ]);
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
  public function import(Request $request) {
    try {
      $count = $this->bucketRepository->import($request->all());

      return response()->json([
        'status' => 200,
        'message' => 'Success',
        'data' => [
          'acceptedImports' => $count,
          'totalJsonEntries' => count($request->all()),
        ],
      ]);
    } catch (Exception $e) {
      throw $e;
      return response()->json([
        'status' => 401,
        'message' => 'Failed to import JSON file',
      ]);
    }
  }

  /* Temporarily removed as API are unused */

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
