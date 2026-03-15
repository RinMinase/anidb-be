<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

use App\Repositories\AppSettingRepository;
use App\Repositories\ElectricityApplianceRepository;
use App\Repositories\ElectricityRepository;
use App\Requests\Electricity\AddEditApplianceRequest;
use App\Requests\Electricity\AddEditElectricityRequest;
use App\Resources\DefaultResponse;
use App\Rules\YearRule;

class ElectricityController extends Controller {

  private AppSettingRepository $appSettingRepository;
  private ElectricityApplianceRepository $applianceRepository;
  private ElectricityRepository $electricityRepository;

  public function __construct(
    AppSettingRepository $appSettingRepository,
    ElectricityApplianceRepository $applianceRepository,
    ElectricityRepository $electricityRepository,
  ) {
    $this->appSettingRepository = $appSettingRepository;
    $this->applianceRepository = $applianceRepository;
    $this->electricityRepository = $electricityRepository;
  }

  #[OA\Get(
    tags: ['Electricity'],
    path: '/api/electricity/readings/',
    summary: 'Electricity - Get all Readings',
    security: [['token' => [], 'api-key' => []]],
    responses: [
      new OA\Response(
        response: 200,
        description: 'Success',
        content: new OA\JsonContent(
          allOf: [
            new OA\Schema(ref: '#/components/schemas/DefaultSuccess'),
            new OA\Schema(
              properties: [
                new OA\Property(
                  property: "data",
                  type: "array",
                  items: new OA\Items(ref: "#/components/schemas/Electricity")
                ),
              ]
            ),
          ]
        )
      ),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function get_all_electricity(): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->electricityRepository->get_all()
    ]);
  }

  #[OA\Post(
    tags: ['Electricity'],
    path: '/api/electricity/readings/',
    summary: 'Electricity - Add a Reading',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(ref: '#/components/parameters/electricity_add_edit_datetime'),
      new OA\Parameter(ref: '#/components/parameters/electricity_add_edit_reading'),
    ],
    responses: [
      new OA\Response(response: 200, ref: '#/components/responses/Success'),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function add_electricity(AddEditElectricityRequest $request): JsonResponse {
    $this->electricityRepository->add($request->only('datetime', 'reading'));

    return DefaultResponse::success();
  }

  #[OA\Put(
    tags: ['Electricity'],
    path: '/api/electricity/readings/{reading_id}',
    summary: 'Electricity - Edit a Reading',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'reading_id',
        description: 'Electricity Reading ID',
        in: 'path',
        required: true,
        example: 1,
        schema: new OA\Schema(type: 'integer', format: 'int32')
      ),
      new OA\Parameter(ref: '#/components/parameters/electricity_add_edit_datetime'),
      new OA\Parameter(ref: '#/components/parameters/electricity_add_edit_reading'),
    ],
    responses: [
      new OA\Response(response: 200, ref: '#/components/responses/Success'),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function edit_electricity(AddEditElectricityRequest $request, $id): JsonResponse {
    $this->electricityRepository->edit($request->only('datetime', 'reading'), $id);

    return DefaultResponse::success();
  }

  #[OA\Delete(
    tags: ['Electricity'],
    path: '/api/electricity/readings/{reading_id}',
    summary: 'Electricity - Delete a Reading',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'reading_id',
        description: 'Electricity Reading ID',
        in: 'path',
        required: true,
        example: 1,
        schema: new OA\Schema(type: 'integer', format: 'int32')
      ),
    ],
    responses: [
      new OA\Response(response: 200, ref: '#/components/responses/Success'),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function delete_electricity($id): JsonResponse {
    $this->electricityRepository->delete($id);

    return DefaultResponse::success();
  }

  #[OA\Get(
    tags: ['Electricity'],
    path: '/api/electricity/per-week',
    summary: 'Electricity - Get chart data per week',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'year',
        in: 'query',
        example: '2020',
        schema: new OA\Schema(ref: '#/components/schemas/YearSchema')
      ),
    ],
    responses: [
      new OA\Response(
        response: 200,
        description: 'Success',
        content: new OA\JsonContent(
          allOf: [
            new OA\Schema(ref: '#/components/schemas/DefaultSuccess'),
            new OA\Schema(
              properties: [
                new OA\Property(
                  property: 'data',
                  type: 'array',
                  items: new OA\Items(
                    properties: []
                  )
                ),
              ]
            ),
          ]
        )
      ),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function get_per_week(Request $request): JsonResponse {
    $values = $request->validated([
      'year' => ['sometimes', new YearRule],
    ]);

    return DefaultResponse::success(null, [
      'data' => $this->electricityRepository->get_per_week($values['year']),
    ]);
  }

  #[OA\Get(
    tags: ['Electricity'],
    path: '/api/electricity/per-month',
    summary: 'Electricity - Get chart data per month',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'year',
        in: 'query',
        example: '2020',
        schema: new OA\Schema(ref: '#/components/schemas/YearSchema')
      ),
    ],
    responses: [
      new OA\Response(
        response: 200,
        description: 'Success',
        content: new OA\JsonContent(
          allOf: [
            new OA\Schema(ref: '#/components/schemas/DefaultSuccess'),
            new OA\Schema(
              properties: [
                new OA\Property(
                  property: 'data',
                  type: 'array',
                  items: new OA\Items(
                    properties: []
                  )
                ),
              ]
            ),
          ]
        )
      ),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function get_per_month(Request $request): JsonResponse {
    $values = $request->validated([
      'year' => ['sometimes', new YearRule],
    ]);

    return DefaultResponse::success(null, [
      'data' => $this->electricityRepository->get_per_month($values['year']),
    ]);
  }

  #[OA\Get(
    tags: ['Electricity'],
    path: '/api/electricity/per-year',
    summary: 'Electricity - Get chart data per year',
    security: [['token' => [], 'api-key' => []]],
    responses: [
      new OA\Response(
        response: 200,
        description: 'Success',
        content: new OA\JsonContent(
          allOf: [
            new OA\Schema(ref: '#/components/schemas/DefaultSuccess'),
            new OA\Schema(
              properties: [
                new OA\Property(
                  property: 'data',
                  type: 'array',
                  items: new OA\Items(
                    properties: []
                  )
                ),
              ]
            ),
          ]
        )
      ),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function get_per_year(): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->electricityRepository->get_per_year(),
    ]);
  }

  #[OA\Post(
    tags: ['Electricity'],
    path: '/api/electricity/change-kwh-setting',
    summary: 'Electricity - Change KWh rate',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        parameter: "electricity_change_kwh_setting_value",
        name: 'value',
        in: 'query',
        required: true,
        example: '15.12',
        schema: new OA\Schema(type: 'number'),
      ),
    ],
    responses: [
      new OA\Response(response: 200, ref: '#/components/responses/Success'),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function change_kwh_setting(Request $request): JsonResponse {
    $values = $request->validate([
      'value' => ['required', 'numeric', 'max:100', 'min:0'],
    ]);

    $this->appSettingRepository->editByKey('kwh_price', $values['value']);

    return DefaultResponse::success();
  }

  #[OA\Get(
    tags: ['Electricity'],
    path: '/api/electricity/appliances',
    summary: 'Electricity - Get appliances per year',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'year',
        in: 'query',
        example: '2020',
        schema: new OA\Schema(ref: '#/components/schemas/YearSchema')
      ),
    ],
    responses: [
      new OA\Response(
        response: 200,
        description: 'Success',
        content: new OA\JsonContent(
          allOf: [
            new OA\Schema(ref: '#/components/schemas/DefaultSuccess'),
            new OA\Schema(
              properties: [
                new OA\Property(
                  property: 'data',
                  type: 'array',
                  items: new OA\Items(
                    properties: [
                      new OA\Property(property: 'id_month', type: 'integer', format: 'int32', example: 1),
                      new OA\Property(property: 'month', type: 'string', example: 'January'),
                      new OA\Property(property: 'month_short', type: 'string', example: 'Jan'),
                      new OA\Property(
                        property: 'names',
                        type: 'array',
                        items: new OA\Items(type: 'string'),
                        example: ['Appliance 1', 'Appliance 2']
                      ),
                    ]
                  )
                ),
              ]
            ),
          ]
        )
      ),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function get_all_appliances(Request $request): JsonResponse {
    $values = $request->validated([
      'year' => ['sometimes', new YearRule],
    ]);

    return DefaultResponse::success(null, [
      'data' => $this->applianceRepository->get_per_month($values['year']),
    ]);
  }

  #[OA\Post(
    tags: ['Electricity'],
    path: '/api/electricity/appliances',
    summary: 'Electricity - Add an appliance',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(ref: '#/components/parameters/electricity_add_edit_appliance_date'),
      new OA\Parameter(ref: '#/components/parameters/electricity_add_edit_appliance_name'),
    ],
    responses: [
      new OA\Response(response: 200, ref: '#/components/responses/Success'),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function add_appliance(AddEditApplianceRequest $request): JsonResponse {
    $values = $request->only('date', 'name');
    $this->applianceRepository->add($values);

    return DefaultResponse::success();
  }

  #[OA\Put(
    tags: ['Electricity'],
    path: '/api/electricity/appliances/{appliance_id}',
    summary: 'Electricity - Edit an appliance',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'appliance_id',
        description: 'Appliance ID',
        in: 'path',
        required: true,
        example: 1,
        schema: new OA\Schema(type: 'integer', format: 'int32')
      ),
      new OA\Parameter(ref: '#/components/parameters/electricity_add_edit_appliance_date'),
      new OA\Parameter(ref: '#/components/parameters/electricity_add_edit_appliance_name'),
    ],
    responses: [
      new OA\Response(response: 200, ref: '#/components/responses/Success'),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function edit_appliance(AddEditApplianceRequest $request, $id): JsonResponse {
    $values = $request->only('date', 'name');
    $this->applianceRepository->edit($values, $id);

    return DefaultResponse::success();
  }

  #[OA\Delete(
    tags: ['Electricity'],
    path: '/api/electricity/appliances/{appliance_id}',
    summary: 'Electricity - Delete an appliance',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'appliance_id',
        description: 'Appliance ID',
        in: 'path',
        required: true,
        example: 1,
        schema: new OA\Schema(type: 'integer', format: 'int32')
      ),
    ],
    responses: [
      new OA\Response(response: 200, ref: '#/components/responses/Success'),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function delete_appliance($id): JsonResponse {
    $this->applianceRepository->delete($id);

    return DefaultResponse::success();
  }
}
