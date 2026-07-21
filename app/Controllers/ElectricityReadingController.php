<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use OpenApi\Attributes as OA;

use App\Exceptions\JsonParsingException;
use App\Repositories\ElectricityReadingRepository;
use App\Requests\Electricity\GetMonthStatsRequest;
use App\Requests\Electricity\GetYearStatsRequest;
use App\Requests\ElectricityReading\AddEditRequest;
use App\Requests\ImportRequest;
use App\Resources\DefaultResponse;

class ElectricityReadingController extends Controller {

  private ElectricityReadingRepository $electricityReadingRepository;

  public function __construct(ElectricityReadingRepository $electricityReadingRepository) {
    $this->electricityReadingRepository = $electricityReadingRepository;
  }

  public function index(): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->electricityReadingRepository->getAll(),
    ]);
  }

  public function get(): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->electricityReadingRepository->get($id),
    ]);
  }

  public function add(AddEditRequest $request): JsonResponse {
    $this->electricityReadingRepository->add($request->only('date', 'reading'));

    return DefaultResponse::success();
  }

  public function edit(AddEditRequest $request, string $id): JsonResponse {
    $this->electricityReadingRepository->edit($request->only('date', 'reading'), $id);

    return DefaultResponse::success();
  }

  public function delete(string $id): JsonResponse {
    $this->codecRepository->deleteAudio($id);

    return DefaultResponse::success();
  }

  #[OA\Get(
    tags: ['Electricity'],
    path: '/api/electricity/readings/month-stats',
    summary: 'Get Electricity Monthly Reading Stats',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(ref: '#/components/parameters/electricity_get_month_stats_year'),
      new OA\Parameter(ref: '#/components/parameters/electricity_get_month_stats_month'),
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
                    new OA\Property(property: 'daily', type: 'object'),
                    new OA\Property(property: 'monthly', type: 'object'),
                  ],
                ),
              ]
            ),
          ]
        )
      ),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function getMonthStats(GetMonthStatsRequest $request): JsonResponse {
    $year = $request->input('year');
    $month = $request->input('month');

    return DefaultResponse::success(null, [
      'data' => $this->electricityReadingRepository->getMonthStats($year, $month),
    ]);
  }

  #[OA\Get(
    tags: ['Electricity'],
    path: '/api/electricity/readings/year-stats',
    summary: 'Get Electricity Yearly Reading Stats',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(ref: '#/components/parameters/electricity_get_year_stats_year'),
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
                    new OA\Property(property: 'daily', type: 'object'),
                    new OA\Property(property: 'monthly', type: 'object'),
                  ],
                ),
              ]
            ),
          ]
        )
      ),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function getYearStats(GetYearStatsRequest $request): JsonResponse {
    $year = $request->input('year');

    return DefaultResponse::success(null, [
      'data' => $this->electricityReadingRepository->getYearStats($year),
    ]);
  }

  #[OA\Post(
    tags: ['Electricity'],
    path: '/api/electricity/readings/import',
    summary: 'Import a JSON file to seed data for electricity readings table',
    security: [['token' => [], 'api-key' => []]],
    requestBody: new OA\RequestBody(
      required: true,
      content: new OA\MediaType(
        mediaType: 'multipart/form-data',
        schema: new OA\Schema(
          properties: [
            new OA\Property(property: 'file', type: 'string', format: 'binary'),
          ]
        )
      )
    ),
    responses: [
      new OA\Response(
        response: 200,
        description: 'Success',
        content: new OA\JsonContent(
          allOf: [
            new OA\Schema(ref: '#/components/schemas/DefaultSuccess'),
            new OA\Schema(
              properties: [
                new OA\Property(property: 'data', ref: '#/components/schemas/DefaultImportSchema'),
              ]
            ),
          ]
        )
      ),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function import(ImportRequest $request): JsonResponse {
    $file = $request->file('file')->get();

    if (!is_json($file)) {
      throw new JsonParsingException();
    }

    $data = json_decode($file);
    $import_count = $this->electricityReadingRepository->import($data);

    return DefaultResponse::success();
  }
}
