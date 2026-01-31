<?php

namespace App\Fourleaf\Controllers;

use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

use App\Controllers\Controller;
use App\Resources\DefaultResponse;

use App\Fourleaf\Repositories\ElectricityRepository;
use App\Fourleaf\Requests\Electricity\AddEditRequest;
use App\Fourleaf\Requests\Electricity\GetRequest;

class ElectricityController extends Controller {
  private ElectricityRepository $electricityRepository;

  public function __construct(ElectricityRepository $electricityRepository) {
    $this->electricityRepository = $electricityRepository;
  }

  #[OA\Get(
    tags: ['Fourleaf - Electricity'],
    path: '/api/fourleaf/electricity',
    summary: 'Fourleaf API - Get Electricity Overview',
    security: [['api-key' => []]],
    parameters: [
      new OA\Parameter(ref: '#/components/parameters/fourleaf_electricity_get_year'),
      new OA\Parameter(ref: '#/components/parameters/fourleaf_electricity_get_month'),
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
                  properties: [
                    new OA\Property(
                      property: 'settings',
                      properties: [
                        new OA\Property(property: 'kwhValue', type: 'number', format: 'float', example: 12.23),
                        new OA\Property(property: 'monthStartsAt', type: 'string', example: 'monday'),
                      ]
                    ),
                    new OA\Property(
                      property: 'weekly',
                      type: 'array',
                      items: new OA\Items(
                        properties: [
                          new OA\Property(property: 'id', type: 'string', format: 'uuid', example: 'e9597119-8452-4f2b-96d8-f2b1b1d2f158'),
                          new OA\Property(property: 'weekNo', type: 'integer', example: 1),
                          new OA\Property(property: 'actualWeekYearNo', type: 'integer', example: 12),
                          new OA\Property(property: 'totalKwh', type: 'number', format: 'float', example: 12.34),
                          new OA\Property(property: 'totalKwhValue', type: 'integer', example: 123),
                          new OA\Property(property: 'daysWithRecord', type: 'integer', example: 123),
                          new OA\Property(property: 'daysWithNoRecord', type: 'integer', example: 123),
                          new OA\Property(property: 'daysInWeek', type: 'integer', example: 123),
                          new OA\Property(property: 'totalRecordedKwh', type: 'integer', example: 123),
                          new OA\Property(property: 'totalEstimatedKwh', type: 'number', format: 'float', example: 12.34),
                          new OA\Property(property: 'estTotalKwh', type: 'number', format: 'float', example: 12.34),
                          new OA\Property(property: 'estTotalPrice', type: 'number', format: 'float', example: 12.34),
                          new OA\Property(property: 'avgDailyKwh', type: 'number', format: 'float', example: 12.34),
                        ]
                      )
                    ),
                    new OA\Property(
                      property: 'daily',
                      type: 'array',
                      items: new OA\Items(
                        properties: [
                          new OA\Property(property: 'id', type: 'string', format: 'uuid', example: 'e9597119-8452-4f2b-96d8-f2b1b1d2f158'),
                          new OA\Property(property: 'dateNumber', type: 'integer', example: 1),
                          new OA\Property(property: 'day', type: 'string', example: 'monday'),
                          new OA\Property(property: 'date', type: 'string', example: '2023-05-21'),
                          new OA\Property(property: 'kwPerHour', type: 'number', format: 'float', example: 12.34),
                          new OA\Property(property: 'kwPerDay', type: 'number', format: 'float', example: 12.34),
                          new OA\Property(property: 'pricePerDay', type: 'number', format: 'float', example: 12.34),
                          new OA\Property(property: 'readingValue', type: 'integer', example: 123),
                          new OA\Property(property: 'readingTime', type: 'string', example: '13:00'),
                          new OA\Property(property: 'state', type: 'string', example: 'low|normal|high'),
                          new OA\Property(property: 'allDaysAvg', type: 'number', format: 'float', example: 12.34),
                        ]
                      )
                    ),
                  ]
                ),
              ]
            ),
          ]
        )
      ),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function get(GetRequest $request): JsonResponse {
    $data = $this->electricityRepository->get(
      $request->get('year'),
      $request->get('month'),
    );

    return DefaultResponse::success(null, [
      'data' => $data,
    ]);
  }

  #[OA\Post(
    tags: ['Fourleaf - Electricity'],
    path: '/api/fourleaf/electricity',
    summary: 'Fourleaf API - Add an Electricity data point',
    security: [['api-key' => []]],
    parameters: [
      new OA\Parameter(ref: '#/components/parameters/fourleaf_electricity_add_edit_datetime'),
      new OA\Parameter(ref: '#/components/parameters/fourleaf_electricity_add_edit_reading'),
    ],
    responses: [
      new OA\Response(response: 200, ref: '#/components/responses/Success'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function add(AddEditRequest $request): JsonResponse {
    $this->electricityRepository->add(
      $request->only(
        'datetime',
        'reading',
      )
    );

    return DefaultResponse::success();
  }

  #[OA\Put(
    tags: ['Fourleaf - Electricity'],
    path: '/api/fourleaf/electricity/{electricity_id}',
    summary: 'Fourleaf API - Edit an Electricity data point',
    security: [['api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'electricity_id',
        description: 'Electricity ID',
        in: 'path',
        required: true,
        example: 1,
        schema: new OA\Schema(type: 'integer', format: 'int32')
      ),
      new OA\Parameter(ref: '#/components/parameters/fourleaf_electricity_add_edit_datetime'),
      new OA\Parameter(ref: '#/components/parameters/fourleaf_electricity_add_edit_reading'),
    ],
    responses: [
      new OA\Response(response: 200, ref: '#/components/responses/Success'),
      new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function edit(AddEditRequest $request, $id): JsonResponse {
    $this->electricityRepository->edit(
      $request->only(
        'datetime',
        'reading',
      ),
      $id
    );

    return DefaultResponse::success();
  }

  #[OA\Delete(
    tags: ['Fourleaf - Electricity'],
    path: '/api/fourleaf/electricity/{electricity_id}',
    summary: 'Fourleaf API - Delete an Electricity data point',
    security: [['api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'electricity_id',
        description: 'Electricity ID',
        in: 'path',
        required: true,
        example: 1,
        schema: new OA\Schema(type: 'integer', format: 'int32')
      ),
    ],
    responses: [
      new OA\Response(response: 200, ref: '#/components/responses/Success'),
      new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function delete($id): JsonResponse {
    $this->electricityRepository->delete($id);

    return DefaultResponse::success();
  }
}
