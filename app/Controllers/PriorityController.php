<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\PriorityRepository;

use App\Resources\DefaultResponse;

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
   *             @OA\Items(ref="#/components/schemas/Priority"),
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
      'data' => $this->priorityRepository->getAll(),
    ]);
  }
}
