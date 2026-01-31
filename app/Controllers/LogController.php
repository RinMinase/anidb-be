<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

use App\Repositories\LogRepository;
use App\Requests\Log\SearchRequest;
use App\Resources\DefaultResponse;

class LogController extends Controller {

  private LogRepository $logRepository;

  public function __construct(LogRepository $logRepository) {
    $this->logRepository = $logRepository;
  }

  #[OA\Get(
    tags: ['Logs'],
    path: '/api/logs',
    summary: 'Get All Logs',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(ref: '#/components/parameters/log_search_column'),
      new OA\Parameter(ref: '#/components/parameters/log_search_order'),
      new OA\Parameter(ref: '#/components/parameters/log_search_page'),
      new OA\Parameter(ref: '#/components/parameters/log_search_limit'),
    ],
    responses: [
      new OA\Response(
        response: 200,
        description: 'OK',
        content: new OA\JsonContent(
          allOf: [
            new OA\Schema(ref: '#/components/schemas/DefaultSuccess'),
            new OA\Schema(ref: '#/components/schemas/Pagination'),
            new OA\Schema(
              properties: [
                new OA\Property(
                  property: 'data',
                  type: 'array',
                  items: new OA\Items(ref: '#/components/schemas/Log')
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
