<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\EntryRepository;
use App\Resources\DefaultResponse;

class EntryByBucketController extends Controller {

  private EntryRepository $entryRepository;

  public function __construct(EntryRepository $entryRepository) {
    $this->entryRepository = $entryRepository;
  }

  /**
   * @OA\Get(
   *   tags={"Entry Specific"},
   *   path="/api/entries/by-bucket",
   *   summary="Get All Bucket Stats with Entries",
   *   security={{"token":{}, "api-key": {}}},
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
   *             @OA\Items(
   *               @OA\Property(property="id", type="integer", format="int32", example=1),
   *               @OA\Property(property="from", type="string", minLength=1, maxLength=1, example="a"),
   *               @OA\Property(property="to", type="string", minLength=1, maxLength=1, example="d"),
   *               @OA\Property(property="free", type="string", example="1.11 TB"),
   *               @OA\Property(property="freeTB", type="string", example="1.11 TB"),
   *               @OA\Property(property="used", type="string", example="123.12 GB"),
   *               @OA\Property(property="percent", type="integer", format="int32", example=10),
   *               @OA\Property(property="total", type="string", example="1.23 TB"),
   *               @OA\Property(
   *                 property="rawTotal",
   *                 type="integer",
   *                 format="int64",
   *                 example=1000169533440,
   *               ),
   *               @OA\Property(property="titles", type="integer", format="int32", example=1),
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
  public function index(): JsonResponse {
    $buckets = $this->entryRepository->getBuckets();

    return DefaultResponse::success(null, [
      'data' => $buckets,
    ]);
  }

  /**
   * @OA\Get(
   *   tags={"Entry Specific"},
   *   path="/api/entries/by-bucket/{bucket_id}",
   *   summary="Get All Entries by Bucket",
   *   security={{"token":{}, "api-key": {}}},
   *
   *   @OA\Parameter(
   *     name="bucket_id",
   *     description="Bucket ID",
   *     in="path",
   *     required=true,
   *     example=1,
   *     @OA\Schema(type="integer", format="int32"),
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
   *             @OA\Items(ref="#/components/schemas/EntrySummaryResource"),
   *           ),
   *           @OA\Property(property="stats", ref="#/components/schemas/Bucket"),
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
    $data = $this->entryRepository->getByBucket($id);

    return DefaultResponse::success(null, [
      'data' => $data['data'],
      'stats' => $data['stats'],
    ]);
  }
}
