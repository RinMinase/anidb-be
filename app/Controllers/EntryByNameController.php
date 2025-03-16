<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\EntryRepository;
use App\Resources\DefaultResponse;
use App\Resources\Entry\EntrySummaryResource;

class EntryByNameController extends Controller {

  private EntryRepository $entryRepository;

  public function __construct(EntryRepository $entryRepository) {
    $this->entryRepository = $entryRepository;
  }

  /**
   * @OA\Get(
   *   tags={"Entry Specific"},
   *   path="/api/entries/by-name",
   *   summary="Get All By Name Stats with Entries",
   *   security={{"token":{}, "api-key": {}}},
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
   *                 property="letter",
   *                 type="string",
   *                 minLength=1,
   *                 maxLength=1,
   *                 example="A",
   *               ),
   *               @OA\Property(property="titles", type="integer", format="int32", example=12),
   *               @OA\Property(property="filesize", type="string", example="12.23 GB"),
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
      'data' => $this->entryRepository->getByName(),
    ]);
  }

  /**
   * @OA\Get(
   *   tags={"Entry Specific"},
   *   path="/api/entries/by-name/{letter}",
   *   summary="Get All Entries by Name",
   *   security={{"token":{}, "api-key": {}}},
   *
   *   @OA\Parameter(
   *     name="letter",
   *     description="A-Z / a-z for alphabet titles or 0 (number) for numeric titles",
   *     in="path",
   *     required=true,
   *     example="A",
   *     @OA\Schema(type="string", pattern="^[a-zA-Z0]$", minLength=1, maxLength=1),
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
  public function get($letter): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => EntrySummaryResource::collection($this->entryRepository->getByLetter($letter)),
    ]);
  }
}
