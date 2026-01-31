<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

use App\Repositories\EntryRepository;
use App\Resources\DefaultResponse;

class EntryBySequenceController extends Controller {

  private EntryRepository $entryRepository;

  public function __construct(EntryRepository $entryRepository) {
    $this->entryRepository = $entryRepository;
  }

  #[OA\Get(
    tags: ['Entry Specific'],
    path: '/api/entries/by-sequence/{sequence_id}',
    summary: 'Get All Sequence Stats with Entries',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'sequence_id',
        description: 'Sequence ID',
        in: 'path',
        required: true,
        example: 1,
        schema: new OA\Schema(type: 'integer', format: 'int32')
      ),
    ],
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
                  items: new OA\Items(ref: '#/components/schemas/EntryBySequenceResource')
                ),
                new OA\Property(
                  property: 'stats',
                  properties: [
                    new OA\Property(property: 'titlesPerDay', type: 'number', example: 1.23),
                    new OA\Property(property: 'epsPerDay', type: 'number', example: 2.34),
                    new OA\Property(property: 'quality2160', type: 'integer', format: 'int32', example: 1),
                    new OA\Property(property: 'quality1080', type: 'integer', format: 'int32', example: 2),
                    new OA\Property(property: 'quality720', type: 'integer', format: 'int32', example: 3),
                    new OA\Property(property: 'quality480', type: 'integer', format: 'int32', example: 4),
                    new OA\Property(property: 'quality360', type: 'integer', format: 'int32', example: 5),
                    new OA\Property(property: 'totalTitles', type: 'integer', format: 'int32', example: 12),
                    new OA\Property(property: 'totalEps', type: 'integer', format: 'int32', example: 123),
                    new OA\Property(property: 'totalSize', type: 'string', example: '12.34 GB'),
                    new OA\Property(property: 'totalDays', type: 'integer', format: 'int32', example: 123),
                    new OA\Property(property: 'startDate', type: 'string', example: 'Jan 01, 2000'),
                    new OA\Property(property: 'endDate', type: 'string', example: 'Feb 01, 2000'),
                  ]
                ),
              ]
            ),
          ]
        )
      ),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function index($id): JsonResponse {
    $data = $this->entryRepository->getBySequence($id);

    return DefaultResponse::success(null, [
      'data' => $data['data']->resource->toArray(),
      'stats' => $data['stats'],
    ]);
  }
}
