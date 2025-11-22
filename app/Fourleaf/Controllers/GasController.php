<?php

namespace App\Fourleaf\Controllers;

use Illuminate\Http\JsonResponse;

use App\Controllers\Controller;
use App\Resources\DefaultResponse;

use App\Requests\ImportRequest;
use App\Fourleaf\Repositories\GasRepository;
use App\Fourleaf\Requests\Gas\AddEditFuelRequest;
use App\Fourleaf\Requests\Gas\AddEditMaintenanceRequest;
use App\Fourleaf\Requests\Gas\GetFuelRequest;
use App\Fourleaf\Requests\Gas\GetOdoRequest;
use App\Fourleaf\Requests\Gas\GetRequest;
use App\Fourleaf\Resources\Gas\MaintenanceResource;

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
   *   security={{"api-key": {}}},
   *
   *   @OA\Parameter(ref="#/components/parameters/fourleaf_gas_get_gas_avg_efficiency_type"),
   *   @OA\Parameter(ref="#/components/parameters/fourleaf_gas_get_gas_efficiency_graph_type"),
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
   *
   *             @OA\Property(
   *               property="stats",
   *               @OA\Property(property="averageEfficiency", type="float", minimum=0, example=12.23),
   *               @OA\Property(property="lastEfficiency", type="float", minimum=0, example=12.23),
   *               @OA\Property(property="mileage", type="integer", format="int32", minimum=0, example=1000),
   *               @OA\Property(property="age", type="string", example="1 year, 2 months"),
   *               @OA\Property(property="kmPerMonth", type="float", minimum=0, example=12.23),
   *             ),
   *
   *             @OA\Property(
   *               property="graph",
   *               @OA\Property(
   *                 property="efficiency",
   *                 @OA\Property(property="<date>", type="string", example="<value>"),
   *                 @OA\Property(property="2020-10-20", type="float", example=12.23),
   *               ),
   *               @OA\Property(
   *                 property="gas",
   *                 @OA\Property(property="<date>", type="string", example="<value>"),
   *                 @OA\Property(property="2020-10-20", type="float", example=12.23),
   *               ),
   *             ),
   *
   *             @OA\Property(
   *               property="maintenance",
   *               @OA\Property(
   *                 property="km",
   *                 @OA\Property(property="engineOil", type="string", example="normal"),
   *                 @OA\Property(property="tires", type="string", example="normal"),
   *                 @OA\Property(property="transmissionFluid", type="string", example="normal"),
   *                 @OA\Property(property="brakeFluid", type="string", example="normal"),
   *                 @OA\Property(property="radiatorFluid", type="string", example="normal"),
   *                 @OA\Property(property="sparkPlugs", type="string", example="normal"),
   *                 @OA\Property(property="powerSteeringFluid", type="string", example="normal"),
   *               ),
   *               @OA\Property(
   *                 property="year",
   *                 @OA\Property(property="engineOil", type="string", example="normal"),
   *                 @OA\Property(property="transmissionFluid", type="string", example="normal"),
   *                 @OA\Property(property="brakeFluid", type="string", example="normal"),
   *                 @OA\Property(property="battery", type="string", example="normal"),
   *                 @OA\Property(property="radiatorFluid", type="string", example="normal"),
   *                 @OA\Property(property="acCoolant", type="string", example="normal"),
   *                 @OA\Property(property="powerSteeringFluid", type="string", example="normal"),
   *                 @OA\Property(property="tires", type="string", example="normal"),
   *               ),
   *             ),
   *
   *             @OA\Property(
   *               property="lastMaintenance",
   *               @OA\Property(
   *                 property="km",
   *                 @OA\Property(property="engineOil", type="string", example="2024-01-01"),
   *                 @OA\Property(property="tires", type="string", example="2024-01-01"),
   *                 @OA\Property(property="transmissionFluid", type="string", example="2024-01-01"),
   *                 @OA\Property(property="brakeFluid", type="string", example="2024-01-01"),
   *                 @OA\Property(property="radiatorFluid", type="string", example="2024-01-01"),
   *                 @OA\Property(property="sparkPlugs", type="string", example="2024-01-01"),
   *                 @OA\Property(property="powerSteeringFluid", type="string", example="2024-01-01"),
   *               ),
   *               @OA\Property(
   *                 property="year",
   *                 @OA\Property(
   *                   property="acCoolant",
   *                   @OA\Property(property="date", type="string", example="2024-01-01"),
   *                   @OA\Property(property="odometer", type="number", minimum=0, maximum=100000, example=2000),
   *                 ),
   *                 @OA\Property(
   *                   property="battery",
   *                   @OA\Property(property="date", type="string", example="2024-01-01"),
   *                   @OA\Property(property="odometer", type="number", minimum=0, maximum=100000, example=2000),
   *                 ),
   *                 @OA\Property(
   *                   property="brakeFluid",
   *                   @OA\Property(property="date", type="string", example="2024-01-01"),
   *                   @OA\Property(property="odometer", type="number", minimum=0, maximum=100000, example=2000),
   *                 ),
   *                 @OA\Property(
   *                   property="engineOil",
   *                   @OA\Property(property="date", type="string", example="2024-01-01"),
   *                   @OA\Property(property="odometer", type="number", minimum=0, maximum=100000, example=2000),
   *                 ),
   *                 @OA\Property(
   *                   property="powerSteeringFluid",
   *                   @OA\Property(property="date", type="string", example="2024-01-01"),
   *                   @OA\Property(property="odometer", type="number", minimum=0, maximum=100000, example=2000),
   *                 ),
   *                 @OA\Property(
   *                   property="radiatorFluid",
   *                   @OA\Property(property="date", type="string", example="2024-01-01"),
   *                   @OA\Property(property="odometer", type="number", minimum=0, maximum=100000, example=2000),
   *                 ),
   *                 @OA\Property(
   *                   property="sparkPlugs",
   *                   @OA\Property(property="date", type="string", example="2024-01-01"),
   *                   @OA\Property(property="odometer", type="number", minimum=0, maximum=100000, example=2000),
   *                 ),
   *                 @OA\Property(
   *                   property="tires",
   *                   @OA\Property(property="date", type="string", example="2024-01-01"),
   *                   @OA\Property(property="odometer", type="number", minimum=0, maximum=100000, example=2000),
   *                 ),
   *                 @OA\Property(
   *                   property="transmissionFluid",
   *                   @OA\Property(property="date", type="string", example="2024-01-01"),
   *                   @OA\Property(property="odometer", type="number", minimum=0, maximum=100000, example=2000),
   *                 ),
   *               ),
   *             ),
   *           ),
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function get(GetRequest $request): JsonResponse {
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
    $data = $this->gasRepository->get(
      $request->get('avg_efficiency_type'),
      $request->get('efficiency_graph_type'),
    );

    return DefaultResponse::success(null, [
      'data' => $data,
    ]);
  }

  /**
   * @OA\Get(
   *   tags={"Fourleaf - Gas"},
   *   path="/api/fourleaf/gas/odo",
   *   summary="Fourleaf API - Get Odometer by Year",
   *   security={{"api-key": {}}},
   *
   *   @OA\Parameter(ref="#/components/parameters/fourleaf_gas_get_odo_year"),
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
   *             example={123, 234},
   *             @OA\Items(schema="integer"),
   *           ),
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/FourleafGasInvalidYearResponse"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function getOdo(GetOdoRequest $request): JsonResponse {
    $data = $this->gasRepository->getOdo($request->get('year'));

    return DefaultResponse::success(null, [
      'data' => $data,
    ]);
  }

  /**
   * @OA\Get(
   *   tags={"Fourleaf - Gas"},
   *   path="/api/fourleaf/gas/fuel",
   *   summary="Fourleaf API - Get Fuel List",
   *   security={{"api-key": {}}},
   *
   *   @OA\Parameter(ref="#/components/parameters/fourleaf_gas_get_fuel_column"),
   *   @OA\Parameter(ref="#/components/parameters/fourleaf_gas_get_fuel_order"),
   *   @OA\Parameter(ref="#/components/parameters/fourleaf_gas_get_fuel_page"),
   *   @OA\Parameter(ref="#/components/parameters/fourleaf_gas_get_fuel_limit"),
   *
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       allOf={
   *         @OA\Schema(ref="#/components/schemas/DefaultSuccess"),
   *         @OA\Schema(ref="#/components/schemas/Pagination"),
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
  public function getFuel(GetFuelRequest $request): JsonResponse {
    $data = $this->gasRepository->getFuel(
      $request->only('column', 'order', 'limit', 'page')
    );

    return DefaultResponse::success(null, [
      'data' => $data['data'],
      'meta' => $data['meta'],
    ]);
  }

  /**
   * @OA\Post(
   *   tags={"Fourleaf - Gas"},
   *   path="/api/fourleaf/gas/fuel",
   *   summary="Fourleaf API - Add a Fuel data",
   *   security={{"api-key": {}}},
   *
   *   @OA\Parameter(ref="#/components/parameters/fourleaf_gas_add_edit_fuel_date"),
   *   @OA\Parameter(ref="#/components/parameters/fourleaf_gas_add_edit_fuel_from_bars"),
   *   @OA\Parameter(ref="#/components/parameters/fourleaf_gas_add_edit_fuel_to_bars"),
   *   @OA\Parameter(ref="#/components/parameters/fourleaf_gas_add_edit_fuel_odometer"),
   *   @OA\Parameter(ref="#/components/parameters/fourleaf_gas_add_edit_fuel_price_per_liter"),
   *   @OA\Parameter(ref="#/components/parameters/fourleaf_gas_add_edit_fuel_liters_filled"),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
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

  /**
   * @OA\Put(
   *   tags={"Fourleaf - Gas"},
   *   path="/api/fourleaf/gas/fuel/{gas_id}",
   *   summary="Fourleaf API - Edit a Fuel data",
   *   security={{"api-key": {}}},
   *
   *   @OA\Parameter(
   *     name="gas_id",
   *     description="Gas ID",
   *     in="path",
   *     required=true,
   *     example=1,
   *     @OA\Schema(type="integer", format="int32"),
   *   ),
   *
   *   @OA\Parameter(ref="#/components/parameters/fourleaf_gas_add_edit_fuel_date"),
   *   @OA\Parameter(ref="#/components/parameters/fourleaf_gas_add_edit_fuel_from_bars"),
   *   @OA\Parameter(ref="#/components/parameters/fourleaf_gas_add_edit_fuel_to_bars"),
   *   @OA\Parameter(ref="#/components/parameters/fourleaf_gas_add_edit_fuel_odometer"),
   *   @OA\Parameter(ref="#/components/parameters/fourleaf_gas_add_edit_fuel_price_per_liter"),
   *   @OA\Parameter(ref="#/components/parameters/fourleaf_gas_add_edit_fuel_liters_filled"),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
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

  /**
   * @OA\Delete(
   *   tags={"Fourleaf - Gas"},
   *   path="/api/fourleaf/gas/fuel/{gas_id}",
   *   summary="Fourleaf API - Delete a Fuel data",
   *   security={{"api-key": {}}},
   *
   *   @OA\Parameter(
   *     name="gas_id",
   *     description="Gas ID",
   *     in="path",
   *     required=true,
   *     example=1,
   *     @OA\Schema(type="integer", format="int32"),
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function deleteFuel($id): JsonResponse {
    $this->gasRepository->deleteFuel($id);

    return DefaultResponse::success();
  }

  /**
   * @OA\Get(
   *   tags={"Fourleaf - Gas"},
   *   path="/api/fourleaf/gas/maintenance",
   *   summary="Fourleaf API - Get Maintenance List",
   *   security={{"api-key": {}}},
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
   *             @OA\Items(ref="#/components/schemas/MaintenanceResource"),
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
      'data' => MaintenanceResource::collection(
        $this->gasRepository->getMaintenance()
      ),
    ]);
  }

  public function addMaintenance(AddEditMaintenanceRequest $request) {
    $this->gasRepository->addMaintenance($request->only('date', 'description', 'odometer', 'parts'));

    return DefaultResponse::success();
  }

  public function editMaintenance(AddEditMaintenanceRequest $request, $id): JsonResponse {
    $this->gasRepository->editMaintenance(
      $request->only('date', 'part', 'odometer'),
      $id,
    );

    return DefaultResponse::success();
  }

  public function deleteMaintenance($id): JsonResponse {
    $this->gasRepository->deleteFuel($id);

    return DefaultResponse::success();
  }

  /**
   * @OA\Get(
   *   tags={"Fourleaf - Gas"},
   *   path="/api/fourleaf/gas/maintenance/parts",
   *   summary="Fourleaf API - Get Maintenance Parts List",
   *   security={{"api-key": {}}},
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
   *             @OA\Items(schema="string", example="engine_oil"),
   *           ),
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function getMaintenanceParts(): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->gasRepository->getMaintenanceParts()
    ]);
  }

  /**
   * @OA\Post(
   *   tags={"Fourleaf - Gas"},
   *   path="/api/fourleaf/gas/import",
   *   summary="Import a JSON file to REPLACE existing data for all gas and maintenance tables",
   *   security={{"token":{}, "api-key": {}}},
   *
   *   @OA\RequestBody(
   *     required=true,
   *     @OA\MediaType(
   *       mediaType="multipart/form-data",
   *       @OA\Schema(
   *         type="object",
   *         @OA\Property(property="file", type="string", format="binary"),
   *       ),
   *     ),
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
   *             @OA\Property(property="gas", ref="#/components/schemas/DefaultImportSchema"),
   *             @OA\Property(property="maintenance", ref="#/components/schemas/DefaultImportSchema"),
   *             @OA\Property(property="maintenanceParts", ref="#/components/schemas/DefaultImportSchema"),
   *           ),
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
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
}
