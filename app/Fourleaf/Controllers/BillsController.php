<?php

namespace App\Fourleaf\Controllers;

use Illuminate\Http\JsonResponse;

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

  /**
   * @OA\Get(
   *   tags={"Fourleaf - Bills"},
   *   path="/api/fourleaf/bills/electricity",
   *   summary="Fourleaf API - Get Electricity Bills by Year",
   *
   *   @OA\Parameter(ref="#/components/parameters/fourleaf_bills_electricity_get_year"),
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
   *             @OA\Items(ref="#/components/schemas/BillsElectricity")
   *           ),
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function get(GetBillsElectricityRequest $request): JsonResponse {
    $data = $this->billsRepository->get($request->get('year'));

    return DefaultResponse::success(null, [
      'data' => $data,
    ]);
  }

  /**
   * @OA\Post(
   *   tags={"Fourleaf - Bills"},
   *   path="/api/fourleaf/bills/electricity",
   *   summary="Fourleaf API - Add an Electricity Bill data point",
   *
   *   @OA\Parameter(ref="#/components/parameters/fourleaf_bills_electricity_add_edit_date"),
   *   @OA\Parameter(ref="#/components/parameters/fourleaf_bills_electricity_add_edit_kwh"),
   *   @OA\Parameter(ref="#/components/parameters/fourleaf_bills_electricity_add_edit_cost"),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function add(AddEditBillsElectricityRequest $request): JsonResponse {
    $this->billsRepository->add($request->only('date', 'kwh', 'cost'));

    return DefaultResponse::success();
  }

  /**
   * @OA\Put(
   *   tags={"Fourleaf - Bills"},
   *   path="/api/fourleaf/bills/electricity/{electricity_bill_uuid}",
   *   summary="Fourleaf API - Edit an Electricity Bill data point",
   *
   *   @OA\Parameter(
   *     name="electricity_bill_uuid",
   *     description="Electricity Bill ID",
   *     in="path",
   *     required=true,
   *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *     @OA\Schema(type="string", format="uuid"),
   *   ),
   *
   *   @OA\Parameter(ref="#/components/parameters/fourleaf_bills_electricity_add_edit_date"),
   *   @OA\Parameter(ref="#/components/parameters/fourleaf_bills_electricity_add_edit_kwh"),
   *   @OA\Parameter(ref="#/components/parameters/fourleaf_bills_electricity_add_edit_cost"),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function edit(AddEditBillsElectricityRequest $request, $id): JsonResponse {
    $this->billsRepository->edit($request->only('date', 'kwh', 'cost'), $id);

    return DefaultResponse::success();
  }

  /**
   * @OA\Delete(
   *   tags={"Fourleaf - Bills"},
   *   path="/api/fourleaf/bills/electricity/{electricity_bill_uuid}",
   *   summary="Fourleaf API - Delete an Electricity Bill data point",
   *
   *   @OA\Parameter(
   *     name="electricity_bill_uuid",
   *     description="Electricity Bill ID",
   *     in="path",
   *     required=true,
   *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *     @OA\Schema(type="string", format="uuid"),
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function delete($id): JsonResponse {
    $this->billsRepository->delete($id);

    return DefaultResponse::success();
  }
}
