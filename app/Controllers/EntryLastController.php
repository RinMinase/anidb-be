<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\EntryRepository;

use App\Resources\DefaultResponse;

class EntryLastController extends Controller {

  private EntryRepository $entryRepository;

  public function __construct(EntryRepository $entryRepository) {
    $this->entryRepository = $entryRepository;
  }

  /**
   * @OA\Get(
   *   tags={"Entry Specific"},
   *   path="/api/entries/last",
   *   summary="Get Latest Entries",
   *   security={{"token":{}}},
   *   @OA\Response(
   *     response=200,
   *     description="OK",
   *     @OA\JsonContent(
   *       allOf={
   *         @OA\Schema(ref="#/components/schemas/DefaultSuccess"),
   *         @OA\Schema(
   *           @OA\Property(property="data", ref="#/components/schemas/EntrySummaryResource"),
   *           @OA\Property(property="stats", ref="#/components/schemas/EntryLastStatsResource"),
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function index(): JsonResponse {
    $data = $this->entryRepository->getLast();

    return DefaultResponse::success(null, [
      'data' => $data['data'],
      'stats' => $data['stats'],
    ]);
  }
}
