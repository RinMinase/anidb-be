<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

use App\Repositories\PriorityRepository;

use App\Resources\DefaultResponse;

class PriorityController extends Controller {

  private PriorityRepository $priorityRepository;

  public function __construct(PriorityRepository $priorityRepository) {
    $this->priorityRepository = $priorityRepository;
  }

  #[OA\Get(
    tags: ['Dropdowns'],
    path: '/api/priorities',
    summary: 'Get All Priorities',
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
                  type: 'array',
                  items: new OA\Items(ref: '#/components/schemas/Priority')
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
  public function index(): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->priorityRepository->getAll(),
    ]);
  }
}
