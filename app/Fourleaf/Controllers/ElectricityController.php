<?php

namespace App\Fourleaf\Controllers;

use Illuminate\Http\JsonResponse;

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

  /**
   * @OA\Get(
   *   tags={"Fourleaf - Electricity"},
   *   path="/api/fourleaf/electricity",
   *   summary="Fourleaf API - Get Electricity Overview",
   *
   *   @OA\Parameter(ref="#/components/parameters/fourleaf_electricity_get_year"),
   *   @OA\Parameter(ref="#/components/parameters/fourleaf_electricity_get_month"),
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
   *             @OA\Property(
   *               property="settings",
   *               @OA\Property(property="kwhValue", type="float", example=12.23),
   *               @OA\Property(property="monthStartsAt", type="string", example="monday"),
   *             ),
   *
   *             @OA\Property(
   *               property="weekly",
   *               type="array",
   *               @OA\Items(
   *                 @OA\Property(property="id", type="string", format="uuid", example="e9597119-8452-4f2b-96d8-f2b1b1d2f158"),
   *                 @OA\Property(property="weekNo", type="integer", example=1),
   *                 @OA\Property(property="actualWeekYearNo", type="integer", example=12),
   *                 @OA\Property(property="totalKwh", type="float", example=12.34),
   *                 @OA\Property(property="totalKwhValue", type="integer", example=123),
   *                 @OA\Property(property="daysWithRecord", type="integer", example=123),
   *                 @OA\Property(property="daysWithNoRecord", type="integer", example=123),
   *                 @OA\Property(property="daysInWeek", type="integer", example=123),
   *                 @OA\Property(property="totalRecordedKwh", type="integer", example=123),
   *                 @OA\Property(property="totalEstimatedKwh", type="float", example=12.34),
   *                 @OA\Property(property="estTotalKwh", type="float", example=12.34),
   *                 @OA\Property(property="estTotalPrice", type="float", example=12.34),
   *                 @OA\Property(property="avgDailyKwh", type="float", example=12.34),
   *               ),
   *             ),
   *
   *             @OA\Property(
   *               property="daily",
   *               type="array",
   *               @OA\Items(
   *                 @OA\Property(property="id", type="string", format="uuid", example="e9597119-8452-4f2b-96d8-f2b1b1d2f158"),
   *                 @OA\Property(property="dateNumber", type="integer", example=1),
   *                 @OA\Property(property="day", type="string", example="monday"),
   *                 @OA\Property(property="date", type="string", example="2023-05-21"),
   *                 @OA\Property(property="kwPerHour", type="float", example=12.34),
   *                 @OA\Property(property="kwPerDay", type="float", example=12.34),
   *                 @OA\Property(property="pricePerDay", type="float", example=12.34),
   *                 @OA\Property(property="readingValue", type="integer", example=123),
   *                 @OA\Property(property="readingTime", type="string", example="13:00"),
   *                 @OA\Property(property="state", type="string", example="low|normal|high"),
   *                 @OA\Property(property="allDaysAvg", type="float", example=12.34),
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
    $data = $this->electricityRepository->get(
      $request->get('year'),
      $request->get('month'),
    );

    return DefaultResponse::success(null, [
      'data' => $data,
    ]);
  }

  /**
   * @OA\Post(
   *   tags={"Fourleaf - Electricity"},
   *   path="/api/fourleaf/electricity",
   *   summary="Fourleaf API - Add an Electricity data point",
   *
   *   @OA\Parameter(ref="#/components/parameters/fourleaf_electricity_add_edit_datetime"),
   *   @OA\Parameter(ref="#/components/parameters/fourleaf_electricity_add_edit_reading"),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function add(AddEditRequest $request): JsonResponse {
    $this->electricityRepository->add(
      $request->only(
        'datetime',
        'reading',
      )
    );

    return DefaultResponse::success();
  }

  /**
   * @OA\Put(
   *   tags={"Fourleaf - Electricity"},
   *   path="/api/fourleaf/electricity/{electricity_id}",
   *   summary="Fourleaf API - Edit an Electricity data point",
   *
   *   @OA\Parameter(
   *     name="electricity_id",
   *     description="Electricity ID",
   *     in="path",
   *     required=true,
   *     example=1,
   *     @OA\Schema(type="integer", format="int32"),
   *   ),
   *
   *   @OA\Parameter(ref="#/components/parameters/fourleaf_electricity_add_edit_datetime"),
   *   @OA\Parameter(ref="#/components/parameters/fourleaf_electricity_add_edit_reading"),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
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

  /**
   * @OA\Delete(
   *   tags={"Fourleaf - Electricity"},
   *   path="/api/fourleaf/electricity/{electricity_id}",
   *   summary="Fourleaf API - Delete an Electricity data point",
   *
   *   @OA\Parameter(
   *     name="electricity_id",
   *     description="Electricity ID",
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
  public function delete($id): JsonResponse {
    $this->electricityRepository->delete($id);

    return DefaultResponse::success();
  }
}
