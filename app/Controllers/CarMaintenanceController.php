<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

use App\Controllers\Controller;
use App\Resources\DefaultResponse;

use App\Repositories\CarMaintenanceRepository;
use App\Requests\Car\AddEditMaintenanceRequest;
use App\Resources\Car\MaintenanceResource;

class CarMaintenanceController extends Controller {
  private CarMaintenanceRepository $carMaintenanceRepository;

  public function __construct(CarMaintenanceRepository $carMaintenanceRepository) {
    $this->carMaintenanceRepository = $carMaintenanceRepository;
  }

  #[OA\Get(
    tags: ["Car"],
    path: "/api/gas/maintenance",
    summary: "Fourleaf API - Get Maintenance List",
    security: [["api-key" => []]],
    responses: [
      new OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(
          allOf: [
            new OA\Schema(ref: "#/components/schemas/DefaultSuccess"),
            new OA\Schema(
              properties: [
                new OA\Property(
                  property: "data",
                  type: "array",
                  items: new OA\Items(ref: "#/components/schemas/MaintenanceResource")
                ),
              ]
            ),
          ]
        )
      ),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function getMaintenanceList(): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => MaintenanceResource::collection(
        $this->carMaintenanceRepository->getMaintenanceList()
      ),
    ]);
  }

  #[OA\Get(
    tags: ["Car"],
    path: "/api/gas/maintenance/{maintenance_id}",
    summary: "Fourleaf API - Get Maintenance Item",
    security: [["api-key" => []]],
    parameters: [
      new OA\Parameter(
        name: "gas_maintenance_idid",
        description: "Maintenance ID",
        in: "path",
        required: true,
        example: 1,
        schema: new OA\Schema(type: "integer", format: "int32")
      ),
    ],
    responses: [
      new OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(
          allOf: [
            new OA\Schema(ref: "#/components/schemas/DefaultSuccess"),
            new OA\Schema(
              properties: [
                new OA\Property(
                  property: "data",
                  type: "array",
                  items: new OA\Items(ref: "#/components/schemas/MaintenanceResource")
                ),
              ]
            ),
          ]
        )
      ),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function getMaintenance($id): JsonResponse {
    $data = $this->carMaintenanceRepository->getMaintenance($id);

    return DefaultResponse::success(null, [
      'data' => new MaintenanceResource($data),
    ]);
  }

  public function addMaintenance(AddEditMaintenanceRequest $request) {
    $values = $request->only('date', 'description', 'odometer', 'parts');
    $this->carMaintenanceRepository->addMaintenance($values);

    return DefaultResponse::success();
  }

  public function editMaintenance(AddEditMaintenanceRequest $request, $id): JsonResponse {
    $values = $request->only('date', 'description', 'odometer', 'parts');
    $this->carMaintenanceRepository->editMaintenance($values, $id);

    return DefaultResponse::success();
  }

  public function deleteMaintenance($id): JsonResponse {
    $this->carMaintenanceRepository->deleteMaintenance($id);

    return DefaultResponse::success();
  }

  #[OA\Get(
    tags: ["Car"],
    path: "/api/gas/maintenance/parts",
    summary: "Fourleaf API - Get Maintenance Parts List",
    security: [["api-key" => []]],
    responses: [
      new OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(
          allOf: [
            new OA\Schema(ref: "#/components/schemas/DefaultSuccess"),
            new OA\Schema(
              properties: [
                new OA\Property(property: "data", type: "array", items: new OA\Items(type: "string", example: "engine_oil")),
              ]
            ),
          ]
        )
      ),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function getMaintenanceParts(): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->carMaintenanceRepository->getMaintenanceParts(),
    ]);
  }
}
