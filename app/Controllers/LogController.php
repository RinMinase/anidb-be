<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\LogRepository;

use App\Requests\Log\SearchRequest;

use App\Resources\DefaultResponse;

class LogController extends Controller {

  private LogRepository $logRepository;

  public function __construct(LogRepository $logRepository) {
    $this->logRepository = $logRepository;
  }

  /**
   * @OA\Get(
   *   tags={"Logs"},
   *   path="/api/logs",
   *   summary="Get All Logs",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(ref="#/components/parameters/log_search_column"),
   *   @OA\Parameter(ref="#/components/parameters/log_search_order"),
   *   @OA\Parameter(ref="#/components/parameters/log_search_page"),
   *   @OA\Parameter(ref="#/components/parameters/log_search_limit"),
   *
   *   @OA\Response(
   *     response=200,
   *     description="OK",
   *     @OA\JsonContent(
   *       allOf={
   *         @OA\Schema(ref="#/components/schemas/DefaultSuccess"),
   *         @OA\Schema(ref="#/components/schemas/Pagination"),
   *         @OA\Schema(
   *           @OA\Property(
   *             property="data",
   *             type="array",
   *             @OA\Items(
   *               @OA\Property(
   *                 property="uuid",
   *                 type="string",
   *                 format="uuid",
   *                 example="9ef81943-78f0-4d1c-a831-a59fb5af339c",
   *               ),
   *               @OA\Property(property="tableChanged", type="string", example="marathon"),
   *               @OA\Property(property="idChanged", type="string", example=1),
   *               @OA\Property(
   *                 property="description",
   *                 type="string",
   *                 example="title changed from 'old' to 'new'",
   *               ),
   *               @OA\Property(property="action", type="string", example="add"),
   *               @OA\Property(
   *                 property="createdAt",
   *                 type="string",
   *                 format="date-time",
   *                 example="2020-01-01 00:00:00",
   *               ),
   *             ),
   *           ),
   *         ),
   *       }
   *     )
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function index(SearchRequest $request): JsonResponse {
    $logs = $this->logRepository->getAll(
      $request->only('column', 'order', 'limit', 'page')
    );

    return DefaultResponse::success(null, [
      'data' => $logs['data'],
      'meta' => $logs['meta'],
    ]);
  }
}
