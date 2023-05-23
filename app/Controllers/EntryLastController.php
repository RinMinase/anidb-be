<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\EntryRepository;
use App\Resources\Entry\EntryCollection;

class EntryLastController extends Controller {

  private EntryRepository $entryRepository;

  public function __construct(EntryRepository $entryRepository) {
    $this->entryRepository = $entryRepository;
  }

  /**
   * @OA\Get(
   *   tags={"Entry"},
   *   path="/api/entries/last",
   *   summary="Get Latest Entries",
   *   security={{"token":{}}},
   *   @OA\Response(
   *     response=200,
   *     description="OK",
   *     @OA\JsonContent(
   *       @OA\Property(property="data", ref="#/components/schemas/EntryCollection"),
   *       @OA\Property(
   *         example={
   *           "dateLastEntry": "Apr 01, 2015",
   *           "daysLastEntry": 2974,
   *           "dateOldestEntry": "Jan 01, 2011",
   *           "daysOldestEntry": 4525,
   *           "totalEps": 0,
   *           "totalTitles": 7,
   *           "totalCours": 0,
   *           "titlesPerWeek": 0.01,
   *           "coursPerWeek": 0,
   *           "epsPerWeek": 0,
   *           "epsPerDay": 0,
   *         },
   *         property="stats",
   *         type="object",
   *         @OA\Property(property="dateLastEntry", type="string"),
   *         @OA\Property(property="daysLastEntry", type="integer", format="int32"),
   *         @OA\Property(property="dateOldestEntry", type="string"),
   *         @OA\Property(property="daysOldestEntry", type="integer", format="int32"),
   *         @OA\Property(property="totalEps", type="integer", format="int32"),
   *         @OA\Property(property="totalTitles", type="integer", format="int32"),
   *         @OA\Property(property="titlesPerWeek", type="number"),
   *         @OA\Property(property="coursPerWeek", type="integer", format="int32"),
   *         @OA\Property(property="epsPerWeek", type="integer", format="int32"),
   *         @OA\Property(property="epsPerDay", type="integer", format="int32"),
   *       ),
   *     )
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   * )
   */
  public function index(): JsonResponse {
    return response()->json($this->entryRepository->getLast());
  }
}
