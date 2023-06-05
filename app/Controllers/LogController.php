<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Repositories\LogRepository;

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
   *       @OA\Property(property="data", ref="#/components/schemas/LogCollection"),
   *       @OA\Property(property="meta", ref="#/components/schemas/Pagination"),
   *     )
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function index(Request $request): JsonResponse {
    $logs = $this->logRepository->getAll($request->all());

    return response()->json($logs);
  }
}
