<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

use App\Repositories\EntryRepository;
use App\Resources\DefaultResponse;
use App\Resources\Entry\EntrySummaryResource;

class EntryByNameController extends Controller {

  private EntryRepository $entryRepository;

  public function __construct(EntryRepository $entryRepository) {
    $this->entryRepository = $entryRepository;
  }

  #[OA\Get(
    tags: ['Entry Specific'],
    path: '/api/entries/by-name',
    summary: 'Get All By Name Stats with Entries',
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
                  items: new OA\Items(
                    properties: [
                      new OA\Property(property: 'letter', type: 'string', minLength: 1, maxLength: 1, example: 'A'),
                      new OA\Property(property: 'titles', type: 'integer', format: 'int32', example: 12),
                      new OA\Property(property: 'filesize', type: 'string', example: '12.23 GB'),
                    ]
                  )
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
      'data' => $this->entryRepository->getByName(),
    ]);
  }

  #[OA\Get(
    tags: ['Entry Specific'],
    path: '/api/entries/by-name/{letter}',
    summary: 'Get All Entries by Name',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'letter',
        description: 'A-Z / a-z for alphabet titles or 0 (number) for numeric titles',
        in: 'path',
        required: true,
        example: 'A',
        schema: new OA\Schema(type: 'string', pattern: '^[a-zA-Z0]$', minLength: 1, maxLength: 1)
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
                  items: new OA\Items(ref: '#/components/schemas/EntrySummaryResource')
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
  public function get($letter): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => EntrySummaryResource::collection($this->entryRepository->getByLetter($letter)),
    ]);
  }
}
