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
   *   security={{"token":{}}},
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
   *             ref="#/components/schemas/EntryBySequenceStatsResource",
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
