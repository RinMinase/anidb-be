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
   *           @OA\Property(
   *             property="stats",
   *             @OA\Property(property="dateLastEntry", type="string", example="Apr 01, 2015"),
   *             @OA\Property(property="daysLastEntry", type="integer", format="int32", example=2974),
   *             @OA\Property(property="dateOldestEntry", type="string", example="Jan 01, 2011"),
   *             @OA\Property(property="daysOldestEntry", type="integer", format="int32", example=4525),
   *             @OA\Property(property="totalEps", type="integer", format="int32", example=0),
   *             @OA\Property(property="totalTitles", type="integer", format="int32", example=7),
   *             @OA\Property(property="totalCours", type="integer", format="int32", example=0),
   *             @OA\Property(property="titlesPerWeek", type="number", example=0.01),
   *             @OA\Property(property="coursPerWeek", type="integer", format="int32", example=0),
   *             @OA\Property(property="epsPerWeek", type="integer", format="int32", example=0),
   *             @OA\Property(property="epsPerDay", type="integer", format="int32", example=0),
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
    $data = $this->entryRepository->getLast();

    return DefaultResponse::success(null, [
      'data' => $data['data'],
      'stats' => $data['stats'],
    ]);
  }
}
