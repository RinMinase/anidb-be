<?php

namespace App\Fourleaf\Controllers;

use Illuminate\Http\JsonResponse;

use App\Controllers\Controller;

use App\Resources\DefaultResponse;

use App\Fourleaf\Repositories\GasRepository;

use App\Fourleaf\Requests\GetGasRequest;

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
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function get(GetGasRequest $request): JsonResponse {
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
      'data' => $this->gasRepository->get(
        $request->get('avgEfficiencyType'),
        $request->get('efficiencyGraphType'),
      ),
    ]);
  }

  /**
   * @OA\Get(
   *   tags={"Fourleaf - Gas"},
   *   path="/api/fourleaf/gas/fuel",
   *   summary="Fourleaf API - Get Fuel List",
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
   *             @OA\Items(ref="#/components/schemas/Gas"),
   *           ),
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function getFuel(): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->gasRepository->getFuel(),
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

  /**
   * @OA\Get(
   *   tags={"Fourleaf - Gas"},
   *   path="/api/fourleaf/gas/maintenance",
   *   summary="Fourleaf API - Get Maintenance List",
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
   *             @OA\Items(ref="#/components/schemas/Maintenance"),
   *           ),
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function getMaintenance(): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->gasRepository->getMaintenance(),
    ]);
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
