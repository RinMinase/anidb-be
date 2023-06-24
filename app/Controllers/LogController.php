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
   *             @OA\Items(ref="#/components/schemas/LogResource"),
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
