<?php

namespace App\Fourleaf\Controllers;

use Illuminate\Http\JsonResponse;

use App\Controllers\Controller;

use App\Fourleaf\Repositories\GasRepository;

use App\Resources\DefaultResponse;

class GasController extends Controller {
  private GasRepository $gasRepository;

  public function __construct(GasRepository $gasRepository) {
    $this->gasRepository = $gasRepository;
  }

  /**
   * @OA\Get(
   *   tags={"Fourleaf - Gas"},
   *   path="/api/fourleaf/gas",
   *   summary="Fourleaf API - Get Gas Overview",
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       allOf={
   *         @OA\Schema(ref="#/components/schemas/DefaultSuccess"),
   *         @OA\Schema(
   *           @OA\Property(
   *             property="data",
   *             ref="#/components/schemas/GetGasResource",
   *           ),
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function get(string $avgEfficiencyType, string $efficiencyGraphType): JsonResponse {
    /**
     * Average Efficiency Types:
     * - "all" (default) - all data points are averaged
     * - "last5" - last 5 data points are averaged
     * - "last10" - last 10 data points are averaged
     *
     * Efficiency Graph Types:
     * - "last20data" (default) - last 20 data points
     * - "last12mos" - last 12 months (per month efficiency, averaged)
     */
    return DefaultResponse::success(null, [
      'data' => $this->gasRepository->get(),
    ]);
  }

  public function addFuel($request): JsonResponse {
    $this->gasRepository->addFuel($request);

    return DefaultResponse::success();
  }

  public function editFuel($request, $id): JsonResponse {
    $this->gasRepository->editFuel($request, $id);

    return DefaultResponse::success();
  }

  public function deleteFuel($id): JsonResponse {
    $this->gasRepository->deleteFuel($id);

    return DefaultResponse::success();
  }

  public function addMaintenance($request): JsonResponse {
    $this->gasRepository->addMaintenance($request);

    return DefaultResponse::success();
  }

  public function editMaintenance($request, $id): JsonResponse {
    $this->gasRepository->editMaintenance($request, $id);

    return DefaultResponse::success();
  }

  public function deleteMaintenance($id): JsonResponse {
    $this->gasRepository->deleteFuel($id);

    return DefaultResponse::success();
  }
}
