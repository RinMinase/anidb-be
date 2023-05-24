<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Repositories\BucketSimRepository;

class BucketSimController extends Controller {

  private BucketSimRepository $bucketSimRepository;

  public function __construct(BucketSimRepository $bucketSimRepository) {
    $this->bucketSimRepository = $bucketSimRepository;
  }

  /**
   * @OA\Get(
   *   tags={"BucketSim"},
   *   path="/api/bucket-sims",
   *   summary="Get All Bucket Sims",
   *   security={{"token":{}}},
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       @OA\Property(
   *         property="data",
   *         type="array",
   *         @OA\Items(ref="#/components/schemas/BucketSimInfo"),
   *       ),
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   * )
   */
  public function index(): JsonResponse {
    return response()->json([
      'data' => $this->bucketSimRepository->getAll(),
    ]);
  }

  /**
   * @OA\Get(
   *   tags={"BucketSim"},
   *   path="/api/bucket-sims/{bucket_info_id}",
   *   summary="Get All Entries by Bucket",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="bucket_info_id",
   *     description="Bucket Info ID",
   *     in="path",
   *     required=true,
   *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *     @OA\Schema(type="string", format="uuid"),
   *   ),
   *
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       @OA\Property(property="data", ref="#/components/schemas/BucketStatsWithEntry"),
   *       @OA\Property(property="stats", ref="#/components/schemas/BucketSimInfo"),
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function get($uuid): JsonResponse {
    return response()->json($this->bucketSimRepository->get($uuid));
  }

  public function add(Request $request): JsonResponse {
    try {
      $this->bucketSimRepository->add($request->all());

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

  public function edit(Request $request, $uuid): JsonResponse {
    try {
      $this->bucketSimRepository->edit($request->all(), $uuid);

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

  public function delete($uuid): JsonResponse {
    try {
      $this->bucketSimRepository->delete($uuid);

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

  public function saveBucket($uuid): JsonResponse {
    try {
      $this->bucketSimRepository->save_bucket($uuid);

      return response()->json([
        'status' => 200,
        'message' => 'Success',
      ]);
    } catch (ModelNotFoundException) {
      return response()->json([
        'status' => 401,
        'message' => 'Bucket Sim ID does not exist',
      ], 401);
    }
  }
}
