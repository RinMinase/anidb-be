<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\EntryRepository;

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
   *       @OA\Property(property="data", ref="#/components/schemas/EntryCollection"),
   *       @OA\Property(
   *         example={
   *           "titles_per_day": 0,
   *           "eps_per_day": 0,
   *           "quality_2160": 0,
   *           "quality_1080": 0,
   *           "quality_720": 0,
   *           "quality_480": 0,
   *           "quality_360": 0,
   *           "total_titles": 0,
   *           "total_eps": 0,
   *           "total_size": "0 GB",
   *           "total_days": 0,
   *           "start_date": "Jan 01, 2000",
   *           "end_date": "Feb 01, 2000"
   *         },
   *         property="stats",
   *         type="object",
   *         @OA\Property(property="titles_per_day", type="number"),
   *         @OA\Property(property="eps_per_day", type="number"),
   *         @OA\Property(property="quality_2160", type="integer", format="int32"),
   *         @OA\Property(property="quality_1080", type="integer", format="int32"),
   *         @OA\Property(property="quality_720", type="integer", format="int32"),
   *         @OA\Property(property="quality_480", type="integer", format="int32"),
   *         @OA\Property(property="quality_360", type="integer", format="int32"),
   *         @OA\Property(property="total_titles", type="integer", format="int32"),
   *         @OA\Property(property="total_eps", type="integer", format="int32"),
   *         @OA\Property(property="total_size", type="string"),
   *         @OA\Property(property="total_days", type="integer", format="int32"),
   *         @OA\Property(property="start_date", type="string"),
   *         @OA\Property(property="end_date", type="string"),
   *       ),
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function index($id): JsonResponse {
    return response()->json([
      'data' => $this->entryRepository->getBySequence($id),
    ]);
  }
}
