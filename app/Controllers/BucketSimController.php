<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\BucketSimRepository;

use App\Requests\BucketSim\AddEditRequest;

use App\Resources\Bucket\BucketStatsWithEntryResource;
use App\Resources\DefaultResponse;

class BucketSimController extends Controller {

  private BucketSimRepository $bucketSimRepository;

  public function __construct(BucketSimRepository $bucketSimRepository) {
    $this->bucketSimRepository = $bucketSimRepository;
  }

  /**
   * @OA\Get(
   *   tags={"Bucket Simulation"},
   *   path="/api/bucket-sims",
   *   summary="Get All Bucket Sims",
   *   security={{"token":{}}},
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       allOf={
   *         @OA\Schema(ref="#/components/schemas/DefaultSuccess"),
   *         @OA\Schema(
   *           @OA\Property(
   *             property="data",
   *             type="array",
   *             @OA\Items(ref="#/components/schemas/BucketSimInfo"),
   *           ),
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function index(): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->bucketSimRepository->getAll(),
    ]);
  }

  /**
   * @OA\Get(
   *   tags={"Bucket Simulation"},
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
   *       allOf={
   *         @OA\Schema(ref="#/components/schemas/DefaultSuccess"),
   *         @OA\Schema(
   *           @OA\Property(
   *             property="data",
   *             type="array",
   *             @OA\Items(ref="#/components/schemas/BucketStatsWithEntryResource"),
   *           ),
   *           @OA\Property(property="stats", ref="#/components/schemas/BucketSimInfo"),
   *         ),
   *       }
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function get($uuid): JsonResponse {
    $data = $this->bucketSimRepository->get($uuid);

    return DefaultResponse::success(null, [
      'data' => BucketStatsWithEntryResource::collection($data['data']),
      'stats' => $data['stats'],
    ]);
  }

  /**
   * @OA\Post(
   *   tags={"Bucket Simulation"},
   *   path="/api/bucket-sims",
   *   summary="Add a Bucket Sim",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(ref="#/components/parameters/bucket_sim_add_edit_description"),
   *   @OA\Parameter(ref="#/components/parameters/bucket_sim_add_edit_buckets"),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function add(AddEditRequest $request): JsonResponse {
    $this->bucketSimRepository->add($request->only('description', 'buckets'));

    return DefaultResponse::success();
  }

  /**
   * @OA\Put(
   *   tags={"Bucket Simulation"},
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
   *   @OA\Parameter(ref="#/components/parameters/bucket_sim_add_edit_description"),
   *   @OA\Parameter(ref="#/components/parameters/bucket_sim_add_edit_buckets"),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function edit(AddEditRequest $request, $uuid): JsonResponse {
    $this->bucketSimRepository->edit(
      $request->only('description', 'buckets'),
      $uuid,
    );

    return DefaultResponse::success();
  }

  /**
   * @OA\Delete(
   *   tags={"Bucket Simulation"},
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
   *   tags={"Bucket Simulation"},
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
