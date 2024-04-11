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

  public function getOverview(): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->gasRepository->getOverview(),
    ]);
  }

  public function getGraphDetails(): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->gasRepository->getGraphDetails(),
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
