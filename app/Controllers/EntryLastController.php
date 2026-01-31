<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

use App\Repositories\EntryRepository;
use App\Requests\Entry\LastWatchRequest;
use App\Resources\DefaultResponse;

class EntryLastController extends Controller {

  private EntryRepository $entryRepository;

  public function __construct(EntryRepository $entryRepository) {
    $this->entryRepository = $entryRepository;
  }

  #[OA\Get(
    tags: ['Entry Specific'],
    path: '/api/entries/last',
    summary: 'Get Latest Entries',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(ref: '#/components/parameters/entry_last_items'),
    ],
    responses: [
      new OA\Response(
        response: 200,
        description: 'OK',
        content: new OA\JsonContent(
          allOf: [
            new OA\Schema(ref: '#/components/schemas/DefaultSuccess'),
            new OA\Schema(
              properties: [
                new OA\Property(property: 'data', ref: '#/components/schemas/EntrySummaryResource'),
                new OA\Property(
                  property: 'stats',
                  properties: [
                    new OA\Property(property: 'dateLastEntry', type: 'string', example: 'Apr 01, 2015'),
                    new OA\Property(property: 'daysLastEntry', type: 'integer', format: 'int32', example: 2974),
                    new OA\Property(property: 'dateOldestEntry', type: 'string', example: 'Jan 01, 2011'),
                    new OA\Property(property: 'daysOldestEntry', type: 'integer', format: 'int32', example: 4525),
                    new OA\Property(property: 'totalEps', type: 'integer', format: 'int32', example: 0),
                    new OA\Property(property: 'totalTitles', type: 'integer', format: 'int32', example: 7),
                    new OA\Property(property: 'totalCours', type: 'integer', format: 'int32', example: 0),
                    new OA\Property(property: 'titlesPerWeek', type: 'number', example: 0.01),
                    new OA\Property(property: 'coursPerWeek', type: 'integer', format: 'int32', example: 0),
                    new OA\Property(property: 'epsPerWeek', type: 'integer', format: 'int32', example: 0),
                    new OA\Property(property: 'epsPerDay', type: 'integer', format: 'int32', example: 0),
                    new OA\Property(property: 'hoursWatchedAvgPerWeek', type: 'integer', format: 'int32', example: 0),
                    new OA\Property(property: 'hoursWatchedLastWeek', type: 'integer', format: 'int32', example: 0),
                    new OA\Property(property: 'hoursWatchedLastTwoWeeks', type: 'integer', format: 'int32', example: 0),
                  ]
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
  public function index(LastWatchRequest $request): JsonResponse {
    $data = $this->entryRepository->getLast($request->only('items'));

    return DefaultResponse::success(null, [
      'data' => $data['data'],
      'stats' => $data['stats'],
    ]);
  }
}
