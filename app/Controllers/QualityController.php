<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

use App\Repositories\QualityRepository;

use App\Resources\DefaultResponse;

class QualityController extends Controller {

  private QualityRepository $qualityRepository;

  public function __construct(QualityRepository $qualityRepository) {
    $this->qualityRepository = $qualityRepository;
  }

  #[OA\Get(
    tags: ['Dropdowns'],
    path: '/api/qualities',
    summary: 'Get All Qualities',
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
                  items: new OA\Items(ref: '#/components/schemas/Quality')
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
      'data' => $this->qualityRepository->getAll(),
    ]);
  }
}
