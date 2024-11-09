<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\EntryRepository;
use App\Resources\DefaultResponse;
use App\Resources\Entry\EntrySummaryResource;

class EntryByGenreController extends Controller {

  private EntryRepository $entryRepository;

  public function __construct(EntryRepository $entryRepository) {
    $this->entryRepository = $entryRepository;
  }

  /**
   * @OA\Get(
   *   tags={"Entry Specific"},
   *   path="/api/entries/by-genre",
   *   summary="Get All By Genre Stats with Entries",
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
   *                 property="genre",
   *                 type="string",
   *                 example="Comedy",
   *               ),
   *               @OA\Property(property="count", type="integer", format="int32", example=12),
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
      'data' => $this->entryRepository->getByGenreStats(),
    ]);
  }

  /**
   * @OA\Get(
   *   tags={"Entry Specific"},
   *   path="/api/entries/by-genre/{genre}",
   *   summary="Get All Entries by Genre",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="genre",
   *     description="Genre",
   *     in="path",
   *     required=true,
   *     example="comedy",
   *     @OA\Schema(type="string"),
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
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function get($genre): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => EntrySummaryResource::collection($this->entryRepository->getByGenre($genre)),
    ]);
  }
}
