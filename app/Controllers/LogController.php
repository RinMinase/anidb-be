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
   *   security={{"bearerAuth":{}}},
   *   @OA\Response(
   *     response=200,
   *     description="OK",
   *     @OA\JsonContent(
   *       @OA\Property(
   *         property="data",
   *         type="array",
   *         @OA\Items(ref="#/components/schemas/Log"),
   *       ),
   *       @OA\Property(
   *         property="meta",
   *         type="object",
   *         ref="#/components/schemas/Pagination",
   *       ),
   *     )
   *   ),
   *   @OA\Response(
   *     response=401,
   *     description="Unauthorized",
   *     @OA\JsonContent(ref="#/components/schemas/Unauthorized"),
   *   ),
   * )
   */
  public function index(Request $request): JsonResponse {
    $logs = $this->logRepository->getAll($request->all());

    return response()->json($logs);
  }
}
