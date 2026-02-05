<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

use App\Repositories\EntryRepository;
use App\Resources\DefaultResponse;

class EntryByBucketController extends Controller {

  private EntryRepository $entryRepository;

  public function __construct(EntryRepository $entryRepository) {
    $this->entryRepository = $entryRepository;
  }

  #[OA\Get(
    tags: ['Entry Specific'],
    path: '/api/entries/by-bucket',
    summary: 'Get All Bucket Stats with Entries',
    security: [['token' => [], 'api-key' => []]],
    responses: [
      new OA\Response(
        response: 200,
        description: 'Success',
        content: new OA\JsonContent(
          allOf: [
            new OA\Schema(ref: '#/components/schemas/DefaultSuccess'),
            new OA\Schema(properties: [
              new OA\Property(property: 'data', type: 'array', items: new OA\Items(properties: [
                new OA\Property(property: 'id', type: 'integer', format: 'int32', example: 1),
                new OA\Property(property: 'from', type: 'string', minLength: 1, maxLength: 1, example: 'a'),
                new OA\Property(property: 'to', type: 'string', minLength: 1, maxLength: 1, example: 'd'),
                new OA\Property(property: 'free', type: 'string', example: '1.11 TB'),
                new OA\Property(property: 'freeTB', type: 'string', example: '1.11 TB'),
                new OA\Property(property: 'used', type: 'string', example: '123.12 GB'),
                new OA\Property(property: 'percent', type: 'integer', format: 'int32', example: 10),
                new OA\Property(property: 'total', type: 'string', example: '1.23 TB'),
                new OA\Property(property: 'rawTotal', type: 'integer', format: 'int64', example: 1000169533440),
                new OA\Property(property: 'titles', type: 'integer', format: 'int32', example: 1),
                new OA\Property(property: 'purchaseDate', type: 'string', format: 'date'),
                new OA\Property(property: "lastSixSn", type: "string", example: "ABC123"),
              ])),
            ]),
          ]
        )
      ),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function index(): JsonResponse {
    $buckets = $this->entryRepository->getBuckets();

    return DefaultResponse::success(null, [
      'data' => $buckets,
    ]);
  }

  #[OA\Get(
    tags: ['Entry Specific'],
    path: '/api/entries/by-bucket/{bucket_id}',
    summary: 'Get All Entries by Bucket',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'bucket_id',
        description: 'Bucket ID',
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
                  items: new OA\Items(ref: '#/components/schemas/EntrySummaryResource')
                ),
                new OA\Property(property: 'stats', ref: '#/components/schemas/Bucket'),
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
  public function get($id): JsonResponse {
    $data = $this->entryRepository->getByBucket($id);

    return DefaultResponse::success(null, [
      'data' => $data['data'],
      'stats' => $data['stats'],
    ]);
  }
}
