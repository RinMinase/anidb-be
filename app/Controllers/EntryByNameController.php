<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\EntryRepository;
use App\Resources\Entry\EntryCollection;

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
   *   security={{"token":{}}},
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       @OA\Property(
   *         example={{
   *           "letter": "#",
   *           "titles": 12,
   *           "filesize": "10.25 GB",
   *         }, {
   *           "letter": "A",
   *           "titles": 34,
   *           "filesize": "12.23 GB",
   *         }},
   *         property="data",
   *         type="array",
   *         @OA\Items(
   *           @OA\Property(property="letter", type="string", minLength=1, maxLength=1),
   *           @OA\Property(property="titles", type="integer", format="int32"),
   *           @OA\Property(property="filesize", type="string"),
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
      'data' => $this->entryRepository->getByName(),
    ]);
  }

  /**
   * @OA\Get(
   *   tags={"Entry Specific"},
   *   path="/api/entries/by-name/{letter}",
   *   summary="Get All Entries by Name",
   *   security={{"token":{}}},
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
   *       @OA\Property(property="data", ref="#/components/schemas/EntryCollection"),
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function get($letter): JsonResponse {
    return response()->json([
      'data' => EntryCollection::collection($this->entryRepository->getByLetter($letter)),
    ]);
  }
}
