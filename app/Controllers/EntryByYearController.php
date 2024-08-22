<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\EntryRepository;
use App\Resources\DefaultResponse;

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
   *       allOf={
   *         @OA\Schema(ref="#/components/schemas/DefaultSuccess"),
   *         @OA\Schema(
   *           @OA\Property(
   *             property="data",
   *             type="array",
   *             @OA\Items(
   *               @OA\Property(
   *                 property="year",
   *                 type="integer",
   *                 format="int32",
   *                 nullable=true,
   *                 description="null value on uncategorized entries",
   *                 example="2020",
   *               ),
   *               @OA\Property(
   *                 property="count",
   *                 type="integer",
   *                 format="int32",
   *                 nullable=true,
   *                 description="null value whenever seasons is present; total count of 'null' year",
   *                 example=null,
   *               ),
   *               @OA\Property(
   *                 property="seasons",
   *                 nullable=true,
   *                 description="null value on uncategorized entries",
   *                 @OA\Property(property="Winter", type="integer", format="int32", example=1),
   *                 @OA\Property(property="Spring", type="integer", format="int32", example=2),
   *                 @OA\Property(property="Summer", type="integer", format="int32", example=3),
   *                 @OA\Property(property="Fall", type="integer", format="int32", example=4),
   *               ),
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
    return DefaultResponse::success(null, [
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
   *     @OA\Schema(ref="#/components/schemas/YearSchema"),
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
   *
   *             @OA\Property(
   *               property="Winter",
   *               type="array",
   *               @OA\Items(ref="#/components/schemas/EntrySummaryResource")
   *             ),
   *             @OA\Property(
   *               property="Spring",
   *               type="array",
   *               @OA\Items(ref="#/components/schemas/EntrySummaryResource")
   *             ),
   *             @OA\Property(
   *               property="Summer",
   *               type="array",
   *               @OA\Items(ref="#/components/schemas/EntrySummaryResource")
   *             ),
   *             @OA\Property(
   *               property="Fall",
   *               type="array",
   *               @OA\Items(ref="#/components/schemas/EntrySummaryResource")
   *             ),
   *             @OA\Property(
   *               property="Uncategorized",
   *               type="array",
   *               @OA\Items(ref="#/components/schemas/EntrySummaryResource")
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
  public function get($year): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->entryRepository->getBySeason($year),
    ]);
  }
}
