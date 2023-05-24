<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\EntryRepository;

class EntryByYearController extends Controller {

  private EntryRepository $entryRepository;

  public function __construct(EntryRepository $entryRepository) {
    $this->entryRepository = $entryRepository;
  }

  /**
   * @OA\Get(
   *   tags={"Entry Specific"},
   *   path="/api/entries/by-year",
   *   summary="Get All By Year Stats with Entries",
   *   security={{"token":{}}},
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       @OA\Property(
   *         example={{
   *           "year": null,
   *           "seasons": null,
   *           "count": 2
   *         }, {
   *           "year": 2010,
   *           "seasons": {
   *             "Winter": 1,
   *             "Spring": 1,
   *             "Summer": 2,
   *             "Fall": 2,
   *           },
   *           "count": null
   *         }},
   *         property="data",
   *         type="array",
   *         @OA\Items(
   *           @OA\Property(
   *             property="year",
   *             type="integer",
   *             format="int32",
   *             nullable=true,
   *             description="null value on uncategorized entries",
   *           ),
   *           @OA\Property(
   *             property="seasons",
   *             type="object",
   *             nullable=true,
   *             description="null value on uncategorized entries",
   *             @OA\Property(property="Winter", type="integer", format="int32"),
   *             @OA\Property(property="Spring", type="integer", format="int32"),
   *             @OA\Property(property="Summer", type="integer", format="int32"),
   *             @OA\Property(property="Fall", type="integer", format="int32"),
   *           ),
   *           @OA\Property(
   *             property="count",
   *             type="integer",
   *             format="int32",
   *             nullable=true,
   *             description="null value whenever seasons is present",
   *           ),
   *         ),
   *       ),
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function index(): JsonResponse {
    return response()->json([
      'data' => $this->entryRepository->getByYear(),
    ]);
  }

  /**
   * @OA\Get(
   *   tags={"Entry Specific"},
   *   path="/api/entries/by-year/{year}",
   *   summary="Get All By Year Stats with Entries",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="year",
   *     description="If year is not valid or null is passed, it fetches for uncategorized entries",
   *     in="path",
   *     example=2000,
   *     @OA\Schema(type="integer", format="int32", minimum=1970, maximum=2999),
   *   ),
   *
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       @OA\Property(
   *         property="data",
   *         type="object",
   *         @OA\Property(property="Winter", ref="#/components/schemas/EntryCollection"),
   *         @OA\Property(property="Spring", ref="#/components/schemas/EntryCollection"),
   *         @OA\Property(property="Summer", ref="#/components/schemas/EntryCollection"),
   *         @OA\Property(property="Fall", ref="#/components/schemas/EntryCollection"),
   *         @OA\Property(
   *           property="Uncategorized",
   *           ref="#/components/schemas/EntryCollection",
   *         ),
   *       ),
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function get($year): JsonResponse {
    return response()->json([
      'data' => $this->entryRepository->getBySeason($year),
    ]);
  }
}
