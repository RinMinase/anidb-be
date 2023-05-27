<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Repositories\BucketSimRepository;

use App\Resources\DefaultResponse;

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
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
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
   *   summary="Get All Entries by Bucket Sim",
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

  /**
   * @OA\Post(
   *   tags={"BucketSim"},
   *   path="/api/bucket-sims",
   *   summary="Add a Bucket Sim",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="buckets",
   *     description="Bucket JSON String",
   *     in="query",
   *     required=true,
   *     example="[{""from"":""a"",""to"":""i"",""size"":2000339066880},{""from"":""j"",""to"":""z"",""size"":2000339066880}]",
   *     @OA\Schema(type="string"),
   *   ),
   *   @OA\Parameter(
   *     name="description",
   *     description="Bucket Sim Description",
   *     in="query",
   *     required=true,
   *     example="Sample 2 buckets",
   *     @OA\Schema(type="string"),
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function add(Request $request): JsonResponse {
    $this->bucketSimRepository->add($request->all());

    return DefaultResponse::success();
  }

  /**
   * @OA\Put(
   *   tags={"BucketSim"},
   *   path="/api/bucket-sims/{bucket_sim_id}",
   *   summary="Edit a Bucket Sim",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="bucket_sim_id",
   *     description="Bucket Sim Info ID",
   *     in="path",
   *     required=true,
   *     example="87d66263-269c-4f7c-9fb8-dd78c4408ff6",
   *     @OA\Schema(type="string", format="uuid"),
   *   ),
   *   @OA\Parameter(
   *     name="buckets",
   *     description="Bucket JSON String",
   *     in="query",
   *     required=true,
   *     example="[{""from"":""a"",""to"":""i"",""size"":2000339066880},{""from"":""j"",""to"":""z"",""size"":2000339066880}]",
   *     @OA\Schema(type="string"),
   *   ),
   *   @OA\Parameter(
   *     name="description",
   *     description="Bucket Sim Description",
   *     in="query",
   *     required=true,
   *     example="Sample 2 buckets",
   *     @OA\Schema(type="string"),
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function edit(Request $request, $uuid): JsonResponse {
    $this->bucketSimRepository->edit($request->all(), $uuid);

    return DefaultResponse::success();
  }

  /**
   * @OA\Delete(
   *   tags={"BucketSim"},
   *   path="/api/bucket-sims/{bucket_sim_id}",
   *   summary="Delete a Bucket Sim",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="bucket_sim_id",
   *     description="Bucket Sim Info ID",
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
  public function delete($uuid): JsonResponse {
    $this->bucketSimRepository->delete($uuid);

    return DefaultResponse::success();
  }

  /**
   * @OA\Post(
   *   tags={"BucketSim"},
   *   path="/api/bucket-sims/{bucket_sim_id}",
   *   summary="Save Bucket Sim as Current Bucket",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="bucket_sim_id",
   *     description="Bucket Sim Info ID",
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
  public function saveBucket($uuid): JsonResponse {
    $this->bucketSimRepository->save_bucket($uuid);

    return DefaultResponse::success();
  }
}
