<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\PriorityRepository;

class PriorityController extends Controller {

  private PriorityRepository $priorityRepository;

  public function __construct(PriorityRepository $priorityRepository) {
    $this->priorityRepository = $priorityRepository;
  }

  /**
   * @OA\Get(
   *   tags={"Dropdowns"},
   *   path="/api/priorities",
   *   summary="Get All Priorities",
   *   security={{"token":{}}},
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       @OA\Property(
   *         property="data",
   *         type="array",
   *         @OA\Items(ref="#/components/schemas/Priority"),
   *       ),
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   * )
   */
  public function index(): JsonResponse {
    return response()->json([
      'data' => $this->priorityRepository->getAll(),
    ]);
  }
}
