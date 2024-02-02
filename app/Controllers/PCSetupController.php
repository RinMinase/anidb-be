<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Resources\DefaultResponse;

use App\Repositories\PCSetupRepository;

class PCSetupController extends Controller {

  private PCSetupRepository $pcSetupRepository;

  public function __construct(PCSetupRepository $pcSetupRepository) {
    $this->pcSetupRepository = $pcSetupRepository;
  }

  /**
   * @OA\Get(
   *   tags={"PC Setup"},
   *   path="/api/pc-setups",
   *   summary="Get All PC Setups",
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
   *             @OA\Items(ref="#/components/schemas/PCSetup"),
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
    $setups = $this->pcSetupRepository->getAll();

    return DefaultResponse::success(null, [
      'data' => $setups,
    ]);
  }
}
