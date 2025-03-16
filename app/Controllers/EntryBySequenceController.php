<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\EntryRepository;
use App\Resources\DefaultResponse;

class EntryBySequenceController extends Controller {

  private EntryRepository $entryRepository;

  public function __construct(EntryRepository $entryRepository) {
    $this->entryRepository = $entryRepository;
  }

  /**
   * @OA\Get(
   *   tags={"Entry Specific"},
   *   path="/api/entries/by-sequence/{sequence_id}",
   *   summary="Get All Sequence Stats with Entries",
   *   security={{"token":{}, "api-key": {}}},
   *
   *   @OA\Parameter(
   *     name="sequence_id",
   *     description="Sequence ID",
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
   *             @OA\Items(ref="#/components/schemas/EntryBySequenceResource"),
   *           ),
   *           @OA\Property(
   *             property="stats",
   *             @OA\Property(property="titlesPerDay", type="number", example=1.23),
   *             @OA\Property(property="epsPerDay", type="number", example=2.34),
   *             @OA\Property(property="quality2160", type="integer", format="int32", example=1),
   *             @OA\Property(property="quality1080", type="integer", format="int32", example=2),
   *             @OA\Property(property="quality720", type="integer", format="int32", example=3),
   *             @OA\Property(property="quality480", type="integer", format="int32", example=4),
   *             @OA\Property(property="quality360", type="integer", format="int32", example=5),
   *             @OA\Property(property="totalTitles", type="integer", format="int32", example=12),
   *             @OA\Property(property="totalEps", type="integer", format="int32", example=123),
   *             @OA\Property(property="totalSize", type="string", example="12.34 GB"),
   *             @OA\Property(property="totalDays", type="integer", format="int32", example=123),
   *             @OA\Property(property="startDate", type="string", example="Jan 01, 2000"),
   *             @OA\Property(property="endDate", type="string", example="Feb 01, 2000"),
   *           ),
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function index($id): JsonResponse {
    $data = $this->entryRepository->getBySequence($id);

    return DefaultResponse::success(null, [
      'data' => $data['data']->resource->toArray(),
      'stats' => $data['stats'],
    ]);
  }
}
