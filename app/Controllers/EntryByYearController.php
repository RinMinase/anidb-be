<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\EntryRepository;

use App\Resources\DefaultResponse;
use App\Resources\Entry\EntryByYearResource;
use App\Resources\Entry\EntryByYearSummaryResource;

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
   *             @OA\Items(ref="#/components/schemas/EntryByYearSummaryResource"),
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
      'data' => EntryByYearSummaryResource::collection($this->entryRepository->getByYear()),
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
   *             ref="#/components/schemas/EntryByYearResource",
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
      'data' => new EntryByYearResource($this->entryRepository->getBySeason($year)),
    ]);
  }
}
