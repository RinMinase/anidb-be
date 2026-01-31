<?php

namespace App\Fourleaf\Controllers;

use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

use App\Controllers\Controller;

use App\Resources\DefaultResponse;
use App\Fourleaf\Repositories\BillsRepository;
use App\Fourleaf\Requests\Bills\AddEditBillsElectricityRequest;
use App\Fourleaf\Requests\Bills\GetBillsElectricityRequest;

class BillsController extends Controller {
  private BillsRepository $billsRepository;

  public function __construct(BillsRepository $billsRepository) {
    $this->billsRepository = $billsRepository;
  }

  #[OA\Get(
    tags: ["Fourleaf - Bills"],
    path: "/api/fourleaf/bills/electricity",
    summary: "Fourleaf API - Get Electricity Bills by Year",
    security: [["api-key" => []]],
    parameters: [
      new OA\Parameter(ref: "#/components/parameters/fourleaf_bills_electricity_get_year"),
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
                  items: new OA\Items(ref: "#/components/schemas/BillsElectricity")
                ),
              ]
            ),
          ]
        )
      ),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function get(GetBillsElectricityRequest $request): JsonResponse {
    $data = $this->billsRepository->get($request->get('year'));

    return DefaultResponse::success(null, [
      'data' => $data,
    ]);
  }

  #[OA\Post(
    tags: ["Fourleaf - Bills"],
    path: "/api/fourleaf/bills/electricity",
    summary: "Fourleaf API - Add an Electricity Bill data point",
    security: [["api-key" => []]],
    parameters: [
      new OA\Parameter(ref: "#/components/parameters/fourleaf_bills_electricity_add_edit_date"),
      new OA\Parameter(ref: "#/components/parameters/fourleaf_bills_electricity_add_edit_kwh"),
      new OA\Parameter(ref: "#/components/parameters/fourleaf_bills_electricity_add_edit_cost"),
    ],
    responses: [
      new OA\Response(response: 200, ref: "#/components/responses/Success"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function add(AddEditBillsElectricityRequest $request): JsonResponse {
    $this->billsRepository->add($request->only('date', 'kwh', 'cost'));

    return DefaultResponse::success();
  }

  #[OA\Put(
    tags: ["Fourleaf - Bills"],
    path: "/api/fourleaf/bills/electricity/{electricity_bill_uuid}",
    summary: "Fourleaf API - Edit an Electricity Bill data point",
    security: [["api-key" => []]],
    parameters: [
      new OA\Parameter(
        name: "electricity_bill_uuid",
        description: "Electricity Bill ID",
        in: "path",
        required: true,
        example: "e9597119-8452-4f2b-96d8-f2b1b1d2f158",
        schema: new OA\Schema(type: "string", format: "uuid")
      ),
      new OA\Parameter(ref: "#/components/parameters/fourleaf_bills_electricity_add_edit_date"),
      new OA\Parameter(ref: "#/components/parameters/fourleaf_bills_electricity_add_edit_kwh"),
      new OA\Parameter(ref: "#/components/parameters/fourleaf_bills_electricity_add_edit_cost"),
    ],
    responses: [
      new OA\Response(response: 200, ref: "#/components/responses/Success"),
      new OA\Response(response: 404, ref: "#/components/responses/NotFound"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function edit(AddEditBillsElectricityRequest $request, $uuid): JsonResponse {
    $this->billsRepository->edit($request->only('date', 'kwh', 'cost'), $uuid);

    return DefaultResponse::success();
  }

  #[OA\Delete(
    tags: ["Fourleaf - Bills"],
    path: "/api/fourleaf/bills/electricity/{electricity_bill_uuid}",
    summary: "Fourleaf API - Delete an Electricity Bill data point",
    security: [["api-key" => []]],
    parameters: [
      new OA\Parameter(
        name: "electricity_bill_uuid",
        description: "Electricity Bill ID",
        in: "path",
        required: true,
        example: "e9597119-8452-4f2b-96d8-f2b1b1d2f158",
        schema: new OA\Schema(type: "string", format: "uuid")
      ),
    ],
    responses: [
      new OA\Response(response: 200, ref: "#/components/responses/Success"),
      new OA\Response(response: 404, ref: "#/components/responses/NotFound"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function delete($uuid): JsonResponse {
    $this->billsRepository->delete($uuid);

    return DefaultResponse::success();
  }
}
