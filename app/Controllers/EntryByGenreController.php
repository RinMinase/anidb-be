<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\EntryRepository;
use App\Resources\DefaultResponse;
use App\Resources\Entry\EntrySummaryResource;

class EntryByGenreController extends Controller {

  private EntryRepository $entryRepository;

  public function __construct(EntryRepository $entryRepository) {
    $this->entryRepository = $entryRepository;
  }

  #[OA\Get(
    tags: ['Entry Specific'],
    path: '/api/entries/by-genre',
    summary: 'Get All By Genre Stats with Entries',
    security: [['token' => []], ['api-key' => []]],
    responses: [
      new OA\Response(
        response: 200,
        description: 'Success',
        content: new OA\JsonContent(
          allOf: [
            new OA\Schema(ref: '#/components/schemas/DefaultSuccess'),
            new OA\Schema(properties: [
              new OA\Property(
                property: 'data',
                type: 'array',
                items: new OA\Items(properties: [
                  new OA\Property(property: 'genre', type: 'string', example: 'Comedy'),
                  new OA\Property(property: 'count', type: 'integer', format: 'int32', example: 12),
                ])
              ),
            ]),
          ]
        )
      ),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function index(): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->entryRepository->getByGenreStats(),
    ]);
  }

  #[OA\Get(
    tags: ['Entry Specific'],
    path: '/api/entries/by-genre/{genre}',
    summary: 'Get All Entries by Genre',
    security: [['token' => []], ['api-key' => []]],
    parameters: [
      new OA\Parameter(name: 'genre', in: 'path', required: true, description: 'Genre', example: 'comedy', schema: new OA\Schema(type: 'string')),
    ],
    responses: [
      new OA\Response(
        response: 200,
        description: 'Success',
        content: new OA\JsonContent(
          allOf: [
            new OA\Schema(ref: '#/components/schemas/DefaultSuccess'),
            new OA\Schema(properties: [
              new OA\Property(
                property: 'data',
                type: 'array',
                items: new OA\Items(ref: '#/components/schemas/EntrySummaryResource')
              ),
            ]),
          ]
        )
      ),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function get($genre): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => EntrySummaryResource::collection($this->entryRepository->getByGenre($genre)),
    ]);
  }
}
