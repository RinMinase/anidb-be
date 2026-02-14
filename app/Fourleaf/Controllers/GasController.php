<?php

namespace App\Fourleaf\Controllers;

use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use App\Controllers\Controller;
use App\Resources\DefaultResponse;
use App\Requests\ImportRequest;

use App\Fourleaf\Repositories\GasRepository;
use App\Fourleaf\Requests\Gas\AddEditFuelRequest;
use App\Fourleaf\Requests\Gas\AddEditMaintenanceRequest;
use App\Fourleaf\Requests\Gas\GetEfficiencyRequest;
use App\Fourleaf\Requests\Gas\GetFuelRequest;
use App\Fourleaf\Requests\Gas\GetOdoRequest;
use App\Fourleaf\Resources\Gas\MaintenanceResource;

class GasController extends Controller {
  private GasRepository $gasRepository;

  public function __construct(GasRepository $gasRepository) {
    $this->gasRepository = $gasRepository;
  }

  #[OA\Get(
    tags: ["Fourleaf - Gas"],
    path: "/api/fourleaf/gas",
    summary: "Fourleaf API - Get Gas Overview",
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
                  properties: [
                    new OA\Property(
                      property: "stats",
                      properties: [
                        new OA\Property(property: "averageEfficiency", type: "number", format: "float", minimum: 0, example: 12.23),
                        new OA\Property(property: "lastEfficiency", type: "number", format: "float", minimum: 0, example: 12.23),
                        new OA\Property(property: "mileage", type: "integer", format: "int32", minimum: 0, example: 1000),
                        new OA\Property(property: "age", type: "string", example: "1 year, 2 months"),
                        new OA\Property(property: "kmPerMonth", type: "number", format: "float", minimum: 0, example: 12.23),
                      ]
                    ),
                    new OA\Property(
                      property: "maintenance",
                      properties: [
                        new OA\Property(
                          property: "km",
                          properties: [
                            new OA\Property(property: "engineOil", type: "string", example: "normal"),
                            new OA\Property(property: "tires", type: "string", example: "normal"),
                            new OA\Property(property: "transmissionFluid", type: "string", example: "normal"),
                            new OA\Property(property: "brakeFluid", type: "string", example: "normal"),
                            new OA\Property(property: "radiatorFluid", type: "string", example: "normal"),
                            new OA\Property(property: "sparkPlugs", type: "string", example: "normal"),
                            new OA\Property(property: "powerSteeringFluid", type: "string", example: "normal"),
                          ]
                        ),
                        new OA\Property(
                          property: "year",
                          properties: [
                            new OA\Property(property: "engineOil", type: "string", example: "normal"),
                            new OA\Property(property: "transmissionFluid", type: "string", example: "normal"),
                            new OA\Property(property: "brakeFluid", type: "string", example: "normal"),
                            new OA\Property(property: "battery", type: "string", example: "normal"),
                            new OA\Property(property: "radiatorFluid", type: "string", example: "normal"),
                            new OA\Property(property: "acCoolant", type: "string", example: "normal"),
                            new OA\Property(property: "powerSteeringFluid", type: "string", example: "normal"),
                            new OA\Property(property: "tires", type: "string", example: "normal"),
                          ]
                        ),
                      ]
                    ),
                    new OA\Property(
                      property: "lastMaintenance",
                      properties: [
                        new OA\Property(
                          property: "km",
                          properties: [
                            new OA\Property(property: "engineOil", type: "string", example: "2024-01-01"),
                            new OA\Property(property: "tires", type: "string", example: "2024-01-01"),
                            new OA\Property(property: "transmissionFluid", type: "string", example: "2024-01-01"),
                            new OA\Property(property: "brakeFluid", type: "string", example: "2024-01-01"),
                            new OA\Property(property: "radiatorFluid", type: "string", example: "2024-01-01"),
                            new OA\Property(property: "sparkPlugs", type: "string", example: "2024-01-01"),
                            new OA\Property(property: "powerSteeringFluid", type: "string", example: "2024-01-01"),
                          ]
                        ),
                        new OA\Property(
                          property: "year",
                          properties: [
                            new OA\Property(
                              property: "acCoolant",
                              properties: [
                                new OA\Property(property: "date", type: "string", example: "2024-01-01"),
                                new OA\Property(property: "odometer", type: "number", minimum: 0, maximum: 100000, example: 2000)
                              ]
                            ),
                            new OA\Property(
                              property: "battery",
                              properties: [
                                new OA\Property(property: "date", type: "string", example: "2024-01-01"),
                                new OA\Property(property: "odometer", type: "number", minimum: 0, maximum: 100000, example: 2000)
                              ]
                            ),
                            new OA\Property(
                              property: "brakeFluid",
                              properties: [
                                new OA\Property(property: "date", type: "string", example: "2024-01-01"),
                                new OA\Property(property: "odometer", type: "number", minimum: 0, maximum: 100000, example: 2000)
                              ]
                            ),
                            new OA\Property(
                              property: "engineOil",
                              properties: [
                                new OA\Property(property: "date", type: "string", example: "2024-01-01"),
                                new OA\Property(property: "odometer", type: "number", minimum: 0, maximum: 100000, example: 2000)
                              ]
                            ),
                            new OA\Property(
                              property: "powerSteeringFluid",
                              properties: [
                                new OA\Property(property: "date", type: "string", example: "2024-01-01"),
                                new OA\Property(property: "odometer", type: "number", minimum: 0, maximum: 100000, example: 2000)
                              ]
                            ),
                            new OA\Property(
                              property: "radiatorFluid",
                              properties: [
                                new OA\Property(property: "date", type: "string", example: "2024-01-01"),
                                new OA\Property(property: "odometer", type: "number", minimum: 0, maximum: 100000, example: 2000)
                              ]
                            ),
                            new OA\Property(
                              property: "sparkPlugs",
                              properties: [
                                new OA\Property(property: "date", type: "string", example: "2024-01-01"),
                                new OA\Property(property: "odometer", type: "number", minimum: 0, maximum: 100000, example: 2000)
                              ]
                            ),
                            new OA\Property(
                              property: "tires",
                              properties: [
                                new OA\Property(property: "date", type: "string", example: "2024-01-01"),
                                new OA\Property(property: "odometer", type: "number", minimum: 0, maximum: 100000, example: 2000)
                              ]
                            ),
                            new OA\Property(
                              property: "transmissionFluid",
                              properties: [
                                new OA\Property(property: "date", type: "string", example: "2024-01-01"),
                                new OA\Property(property: "odometer", type: "number", minimum: 0, maximum: 100000, example: 2000)
                              ]
                            ),
                          ]
                        ),
                      ]
                    ),
                  ]
                ),
              ]
            ),
          ]
        )
      ),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function get(): JsonResponse {
    $data = $this->gasRepository->get();

    return DefaultResponse::success(null, [
      'data' => $data,
    ]);
  }

  #[OA\Get(
    tags: ["Fourleaf - Gas"],
    path: "/api/fourleaf/gas/odo",
    summary: "Fourleaf API - Get Odometer by Year",
    security: [["api-key" => []]],
    parameters: [
      new OA\Parameter(ref: "#/components/parameters/fourleaf_gas_get_odo_year"),
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
                  example: [123, 234],
                  items: new OA\Items(type: "integer")
                ),
              ]
            ),
          ]
        )
      ),
      new OA\Response(response: 401, ref: "#/components/responses/FourleafGasInvalidYearResponse"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function getOdo(GetOdoRequest $request): JsonResponse {
    $data = $this->gasRepository->getOdo($request->get('year'));

    return DefaultResponse::success(null, [
      'data' => $data,
    ]);
  }

  #[OA\Get(
    tags: ["Fourleaf - Gas"],
    path: "/api/fourleaf/gas/efficiency",
    summary: "Fourleaf API - Get Gas Efficiency",
    security: [["api-key" => []]],
    parameters: [
      new OA\Parameter(ref: "#/components/parameters/fourleaf_gas_get_gas_efficiency_type"),
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
                  properties: [
                    new OA\Property(property: "2020-10-20", type: "number", format: "float", example: 12.23),
                  ]
                ),
              ]
            ),
          ]
        )
      ),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function getEfficiency(GetEfficiencyRequest $request): JsonResponse {
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

    $data = $this->gasRepository->getEfficiency($request->get('type'));

    return DefaultResponse::success(null, [
      'data' => $data,
    ]);
  }


  #[OA\Get(
    tags: ["Fourleaf - Gas"],
    path: "/api/fourleaf/gas/prices",
    summary: "Fourleaf API - Get Gas Prices",
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
                  properties: [
                    new OA\Property(property: "2020-10-20", type: "number", format: "float", example: 12.23),
                  ]
                ),
              ]
            ),
          ]
        )
      ),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function getPrices(): JsonResponse {
    $data = $this->gasRepository->getPrices();

    return DefaultResponse::success(null, [
      'data' => $data,
    ]);
  }

  #[OA\Get(
    tags: ["Fourleaf - Gas"],
    path: "/api/fourleaf/gas/fuel",
    summary: "Fourleaf API - Get Fuel List",
    security: [["api-key" => []]],
    parameters: [
      new OA\Parameter(ref: "#/components/parameters/fourleaf_gas_get_fuel_column"),
      new OA\Parameter(ref: "#/components/parameters/fourleaf_gas_get_fuel_order"),
      new OA\Parameter(ref: "#/components/parameters/fourleaf_gas_get_fuel_page"),
      new OA\Parameter(ref: "#/components/parameters/fourleaf_gas_get_fuel_limit"),
    ],
    responses: [
      new OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(
          allOf: [
            new OA\Schema(ref: "#/components/schemas/DefaultSuccess"),
            new OA\Schema(ref: "#/components/schemas/Pagination"),
            new OA\Schema(
              properties: [
                new OA\Property(property: "data", type: "array", items: new OA\Items(ref: "#/components/schemas/Gas")),
              ]
            ),
          ]
        )
      ),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function getFuelList(GetFuelRequest $request): JsonResponse {
    $data = $this->gasRepository->getFuelList(
      $request->only('column', 'order', 'limit', 'page')
    );

    return DefaultResponse::success(null, [
      'data' => $data['data'],
      'meta' => $data['meta'],
    ]);
  }

  #[OA\Get(
    tags: ["Fourleaf - Gas"],
    path: "/api/fourleaf/gas/fuel/{gas_id}",
    summary: "Fourleaf API - Get Fuel Item",
    security: [["api-key" => []]],
    parameters: [
      new OA\Parameter(
        name: "gas_id",
        description: "Gas ID",
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
                new OA\Property(property: "data", ref: "#/components/schemas/Gas"),
              ]
            ),
          ]
        )
      ),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function getFuel($id): JsonResponse {
    $data = $this->gasRepository->getFuel($id);

    return DefaultResponse::success(null, [
      'data' => $data,
    ]);
  }

  #[OA\Post(
    path: "/api/fourleaf/gas/fuel",
    summary: "Fourleaf API - Add a Fuel data",
    security: [["api-key" => []]],
    tags: ["Fourleaf - Gas"],
    parameters: [
      new OA\Parameter(ref: "#/components/parameters/fourleaf_gas_add_edit_fuel_date"),
      new OA\Parameter(ref: "#/components/parameters/fourleaf_gas_add_edit_fuel_from_bars"),
      new OA\Parameter(ref: "#/components/parameters/fourleaf_gas_add_edit_fuel_to_bars"),
      new OA\Parameter(ref: "#/components/parameters/fourleaf_gas_add_edit_fuel_odometer"),
      new OA\Parameter(ref: "#/components/parameters/fourleaf_gas_add_edit_fuel_price_per_liter"),
      new OA\Parameter(ref: "#/components/parameters/fourleaf_gas_add_edit_fuel_liters_filled")
    ],
    responses: [
      new OA\Response(response: 200, ref: "#/components/responses/Success"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed")
    ]
  )]
  public function addFuel(AddEditFuelRequest $request): JsonResponse {
    $this->gasRepository->addFuel(
      $request->only(
        'date',
        'from_bars',
        'to_bars',
        'odometer',
        'price_per_liter',
        'liters_filled',
      )
    );

    return DefaultResponse::success();
  }

  #[OA\Put(
    tags: ["Fourleaf - Gas"],
    path: "/api/fourleaf/gas/fuel/{gas_id}",
    summary: "Fourleaf API - Edit a Fuel data",
    security: [["api-key" => []]],
    parameters: [
      new OA\Parameter(
        name: "gas_id",
        description: "Gas ID",
        in: "path",
        required: true,
        example: 1,
        schema: new OA\Schema(type: "integer", format: "int32")
      ),
      new OA\Parameter(ref: "#/components/parameters/fourleaf_gas_add_edit_fuel_date"),
      new OA\Parameter(ref: "#/components/parameters/fourleaf_gas_add_edit_fuel_from_bars"),
      new OA\Parameter(ref: "#/components/parameters/fourleaf_gas_add_edit_fuel_to_bars"),
      new OA\Parameter(ref: "#/components/parameters/fourleaf_gas_add_edit_fuel_odometer"),
      new OA\Parameter(ref: "#/components/parameters/fourleaf_gas_add_edit_fuel_price_per_liter"),
      new OA\Parameter(ref: "#/components/parameters/fourleaf_gas_add_edit_fuel_liters_filled"),
    ],
    responses: [
      new OA\Response(response: 200, ref: "#/components/responses/Success"),
      new OA\Response(response: 404, ref: "#/components/responses/NotFound"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function editFuel(AddEditFuelRequest $request, $id): JsonResponse {
    $this->gasRepository->editFuel(
      $request->only(
        'date',
        'from_bars',
        'to_bars',
        'odometer',
        'price_per_liter',
        'liters_filled',
      ),
      $id
    );

    return DefaultResponse::success();
  }

  #[OA\Delete(
    tags: ["Fourleaf - Gas"],
    path: "/api/fourleaf/gas/fuel/{gas_id}",
    summary: "Fourleaf API - Delete a Fuel data",
    security: [["api-key" => []]],
    parameters: [
      new OA\Parameter(name: "gas_id", description: "Gas ID", in: "path", required: true, example: 1, schema: new OA\Schema(type: "integer", format: "int32")),
    ],
    responses: [
      new OA\Response(response: 200, ref: "#/components/responses/Success"),
      new OA\Response(response: 404, ref: "#/components/responses/NotFound"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function deleteFuel($id): JsonResponse {
    $this->gasRepository->deleteFuel($id);

    return DefaultResponse::success();
  }

  #[OA\Get(
    tags: ["Fourleaf - Gas"],
    path: "/api/fourleaf/gas/maintenance",
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
        $this->gasRepository->getMaintenanceList()
      ),
    ]);
  }

  #[OA\Get(
    tags: ["Fourleaf - Gas"],
    path: "/api/fourleaf/gas/maintenance/{maintenance_id}",
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
    $data = $this->gasRepository->getMaintenance($id);

    return DefaultResponse::success(null, [
      'data' => new MaintenanceResource($data),
    ]);
  }

  public function addMaintenance(AddEditMaintenanceRequest $request) {
    $values = $request->only('date', 'description', 'odometer', 'parts');
    $this->gasRepository->addMaintenance($values);

    return DefaultResponse::success();
  }

  public function editMaintenance(AddEditMaintenanceRequest $request, $id): JsonResponse {
    $values = $request->only('date', 'description', 'odometer', 'parts');
    $this->gasRepository->editMaintenance($values, $id);

    return DefaultResponse::success();
  }

  public function deleteMaintenance($id): JsonResponse {
    $this->gasRepository->deleteMaintenance($id);

    return DefaultResponse::success();
  }

  #[OA\Get(
    tags: ["Fourleaf - Gas"],
    path: "/api/fourleaf/gas/maintenance/parts",
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
      'data' => $this->gasRepository->getMaintenanceParts(),
    ]);
  }

  #[OA\Post(
    tags: ["Fourleaf - Gas"],
    path: "/api/fourleaf/gas/import",
    summary: "Fourleaf API - Import a JSON file to REPLACE existing data for all gas and maintenance tables",
    security: [["token" => [], "api-key" => []]],
    requestBody: new OA\RequestBody(
      required: true,
      content: new OA\MediaType(
        mediaType: "multipart/form-data",
        schema: new OA\Schema(
          properties: [
            new OA\Property(property: "file", type: "string", format: "binary"),
          ]
        )
      )
    ),
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
                  properties: [
                    new OA\Property(property: "gas", ref: "#/components/schemas/DefaultImportSchema"),
                    new OA\Property(property: "maintenance", ref: "#/components/schemas/DefaultImportSchema"),
                    new OA\Property(property: "maintenanceParts", ref: "#/components/schemas/DefaultImportSchema"),
                  ]
                ),
              ]
            ),
          ]
        )
      ),
      new OA\Response(response: 401, ref: "#/components/responses/Unauthorized"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function import(ImportRequest $request): JsonResponse {
    $file = $request->file('file')->get();

    if (!is_json($file)) {
      throw new JsonParsingException();
    }

    $data = json_decode($file);

    $countGasData = 0;
    $totalGasData = 0;
    $countMaintenanceData = 0;
    $totalMaintenanceData = 0;
    $countMaintenancePartsData = 0;
    $totalMaintenancePartsData = 0;

    if (isset($data->gas) || isset($data->maintenance)) {
      $totalGasData = count($data->gas);
      $totalMaintenanceData = count($data->maintenance);

      if (is_array($data->maintenance)) {
        foreach ($data->maintenance as $item) {
          if (!empty($item->parts) && is_array($item->parts)) {
            $totalMaintenancePartsData += count($item->parts);
          }
        }
      }

      $importCounts = $this->gasRepository->import($data->gas, $data->maintenance);
      $countGasData = $importCounts['gas'];
      $countMaintenanceData = $importCounts['maintenance'];
      $countMaintenancePartsData = $importCounts['parts'];
    }

    return DefaultResponse::success(null, [
      'data' => [
        'gas' => [
          'acceptedImports' => $countGasData,
          'totalJsonEntries' => $totalGasData,
        ],
        'maintenance' => [
          'acceptedImports' => $countMaintenanceData,
          'totalJsonEntries' => $totalMaintenanceData,
        ],
        'maintenanceParts' => [
          'acceptedImports' => $countMaintenancePartsData,
          'totalJsonEntries' => $totalMaintenancePartsData,
        ]
      ],
    ]);
  }

  #[OA\Post(
    tags: ["Fourleaf - Gas"],
    path: "/api/fourleaf/gas/export",
    summary: "Fourleaf API - Export all Fuel and Maintenance data",
    security: [["api-key" => []]],
    responses: [
      new OA\Response(response: 200, description: "OK", content: new OA\JsonContent(type: "string", format: "binary")),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function export(): BinaryFileResponse {
    $data = $this->gasRepository->export();

    return response()->download($data['file'], $data['filename'], $data['headers']);
  }

  public function getGuide(): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->gasRepository->getGuide()
    ]);
  }
}
