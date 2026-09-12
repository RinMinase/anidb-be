<?php

namespace App\Controllers;

use App\Exceptions\JsonParsingException;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

use App\Repositories\ElectricityBillRepository;
use App\Requests\Electricity\AddEditReadingRequest;
use App\Requests\Electricity\GetReadingsRequest;
use App\Requests\Electricity\GetYearStatsRequest;
use App\Requests\ImportRequest;
use App\Resources\DefaultResponse;

class ElectricityBillController extends Controller {

  private ElectricityBillRepository $electricityBillRepository;

  public function __construct(ElectricityBillRepository $electricityBillRepository) {
    $this->electricityBillRepository = $electricityBillRepository;
  }

  #[OA\Get(
    tags: ['Electricity'],
    path: '/api/electricity/bills/',
    summary: 'Get All Electricity Bills',
    security: [['token' => [], 'api-key' => []]],
    responses: [
      new OA\Response(
        response: 200,
        description: 'OK',
        content: new OA\JsonContent(
          allOf: [
            new OA\Schema(ref: '#/components/schemas/DefaultSuccess'),
            new OA\Schema(
              properties: [
                new OA\Property(
                  property: 'data',
                  type: 'array',
                  items: new OA\Items(ref: '#/components/schemas/ElectricityBill')
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
  public function index(GetReadingsRequest $request): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->electricityBillRepository->getAll(),
    ]);
  }

  #[OA\Get(
    tags: ['Electricity'],
    path: '/api/electricity/bills/{bill_id}',
    summary: 'Get Electricity Bill',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'bill_id',
        description: 'Bill ID',
        in: 'path',
        required: true,
        example: 1,
        schema: new OA\Schema(type: 'integer', format: 'int32')
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
                new OA\Property(property: 'data', ref: '#/components/schemas/ElectricityBill'),
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
  public function get(string $id): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->electricityBillRepository->get($id),
    ]);
  }

  #[OA\Post(
    tags: ['Electricity'],
    path: '/api/electricity/bills',
    summary: 'Add an Electricity Bill',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(ref: "#/components/parameters/electricity_add_edit_bill_bill_date"),
      new OA\Parameter(ref: "#/components/parameters/electricity_add_edit_bill_reading_total"),
      new OA\Parameter(ref: "#/components/parameters/electricity_add_edit_bill_total_cost"),
      new OA\Parameter(ref: "#/components/parameters/electricity_add_edit_bill_period_from"),
      new OA\Parameter(ref: "#/components/parameters/electricity_add_edit_bill_period_to"),
      new OA\Parameter(ref: "#/components/parameters/electricity_add_edit_bill_avg_kwh"),
      new OA\Parameter(ref: "#/components/parameters/electricity_add_edit_bill_reading_from"),
      new OA\Parameter(ref: "#/components/parameters/electricity_add_edit_bill_reading_to"),
      new OA\Parameter(ref: "#/components/parameters/electricity_add_edit_bill_price_generation"),
      new OA\Parameter(ref: "#/components/parameters/electricity_add_edit_bill_price_distribution"),
      new OA\Parameter(ref: "#/components/parameters/electricity_add_edit_bill_price_govt"),
      new OA\Parameter(ref: "#/components/parameters/electricity_add_edit_bill_kwh_price_generation_charge"),
      new OA\Parameter(ref: "#/components/parameters/electricity_add_edit_bill_kwh_price_distribution_charge"),
      new OA\Parameter(ref: "#/components/parameters/electricity_add_edit_bill_notes"),
    ],
    responses: [
      new OA\Response(response: 200, ref: '#/components/responses/Success'),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function add(AddEditReadingRequest $request): JsonResponse {
    $this->electricityBillRepository->add($request->only('date', 'reading'));

    return DefaultResponse::success();
  }

  #[OA\Put(
    tags: ['Electricity'],
    path: '/api/electricity/bills/{bill_id}',
    summary: 'Edit an Electricity Bill',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'bill_id',
        description: 'Bill ID',
        in: 'path',
        required: true,
        example: 1,
        schema: new OA\Schema(type: 'integer', format: 'int32')
      ),
      new OA\Parameter(ref: "#/components/parameters/electricity_add_edit_bill_bill_date"),
      new OA\Parameter(ref: "#/components/parameters/electricity_add_edit_bill_reading_total"),
      new OA\Parameter(ref: "#/components/parameters/electricity_add_edit_bill_total_cost"),
      new OA\Parameter(ref: "#/components/parameters/electricity_add_edit_bill_period_from"),
      new OA\Parameter(ref: "#/components/parameters/electricity_add_edit_bill_period_to"),
      new OA\Parameter(ref: "#/components/parameters/electricity_add_edit_bill_avg_kwh"),
      new OA\Parameter(ref: "#/components/parameters/electricity_add_edit_bill_reading_from"),
      new OA\Parameter(ref: "#/components/parameters/electricity_add_edit_bill_reading_to"),
      new OA\Parameter(ref: "#/components/parameters/electricity_add_edit_bill_price_generation"),
      new OA\Parameter(ref: "#/components/parameters/electricity_add_edit_bill_price_distribution"),
      new OA\Parameter(ref: "#/components/parameters/electricity_add_edit_bill_price_govt"),
      new OA\Parameter(ref: "#/components/parameters/electricity_add_edit_bill_kwh_price_generation_charge"),
      new OA\Parameter(ref: "#/components/parameters/electricity_add_edit_bill_kwh_price_distribution_charge"),
      new OA\Parameter(ref: "#/components/parameters/electricity_add_edit_bill_notes"),
    ],
    responses: [
      new OA\Response(response: 200, ref: '#/components/responses/Success'),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function edit(AddEditReadingRequest $request, string $id): JsonResponse {
    $this->electricityBillRepository->edit($request->only('date', 'reading'), $id);

    return DefaultResponse::success();
  }

  #[OA\Delete(
    tags: ["Electricity"],
    path: "/api/electricity/bills/{bill_id}",
    summary: "Delete an Electricity Bill",
    security: [["token" => [], "api-key" => []]],
    parameters: [
      new OA\Parameter(
        name: 'bill_id',
        description: 'Bill ID',
        in: 'path',
        required: true,
        example: 1,
        schema: new OA\Schema(type: 'integer', format: 'int32')
      ),
    ],
    responses: [
      new OA\Response(response: 200, ref: "#/components/responses/Success"),
      new OA\Response(response: 401, ref: "#/components/responses/Unauthorized"),
      new OA\Response(response: 404, ref: "#/components/responses/NotFound"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function delete(string $id): JsonResponse {
    $this->electricityBillRepository->delete($id);

    return DefaultResponse::success();
  }

  #[OA\Get(
    tags: ['Electricity'],
    path: '/api/electricity/bills/stats',
    summary: 'Get Electricity Bill Stats',
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
  public function getStats(): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->electricityBillRepository->getStats(),
    ]);
  }

  #[OA\Post(
    tags: ['Electricity'],
    path: '/api/electricity/bills/import',
    summary: 'Import a JSON file to seed data for electricity bills table',
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
    $import_count = $this->electricityBillRepository->import($data);

    return DefaultResponse::success();
  }
}
