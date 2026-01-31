<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

use App\Repositories\EntryRepository;
use App\Resources\DefaultResponse;

class EntryByYearController extends Controller {

  private EntryRepository $entryRepository;

  public function __construct(EntryRepository $entryRepository) {
    $this->entryRepository = $entryRepository;
  }

  #[OA\Get(
    tags: ['Entry Specific'],
    path: '/api/entries/by-year',
    summary: 'Get All By Year Stats with Entries',
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
                      new OA\Property(
                        property: 'year',
                        type: 'integer',
                        format: 'int32',
                        nullable: true,
                        description: 'null value on uncategorized entries',
                        example: 2020
                      ),
                      new OA\Property(
                        property: 'count',
                        type: 'integer',
                        format: 'int32',
                        nullable: true,
                        description: "null value whenever seasons is present; total count of 'null' year",
                        example: null
                      ),
                      new OA\Property(
                        property: 'seasons',
                        nullable: true,
                        description: 'null value on uncategorized entries',
                        properties: [
                          new OA\Property(property: 'Winter', type: 'integer', format: 'int32', example: 1),
                          new OA\Property(property: 'Spring', type: 'integer', format: 'int32', example: 2),
                          new OA\Property(property: 'Summer', type: 'integer', format: 'int32', example: 3),
                          new OA\Property(property: 'Fall', type: 'integer', format: 'int32', example: 4),
                        ]
                      ),
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
      'data' => $this->entryRepository->getByYear(),
    ]);
  }

  #[OA\Get(
    tags: ['Entry Specific'],
    path: '/api/entries/by-year/{year}',
    summary: 'Get All By Year Stats with Entries',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'year',
        description: "Year should be between 1900-2999, pass 'uncategorized' if fetching for uncategorized",
        in: 'path',
        example: 2000,
        schema: new OA\Schema(ref: '#/components/schemas/YearSchema')
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
                  properties: [
                    new OA\Property(
                      property: 'Winter',
                      type: 'array',
                      items: new OA\Items(ref: '#/components/schemas/EntrySummaryResource')
                    ),
                    new OA\Property(
                      property: 'Spring',
                      type: 'array',
                      items: new OA\Items(ref: '#/components/schemas/EntrySummaryResource')
                    ),
                    new OA\Property(
                      property: 'Summer',
                      type: 'array',
                      items: new OA\Items(ref: '#/components/schemas/EntrySummaryResource')
                    ),
                    new OA\Property(
                      property: 'Fall',
                      type: 'array',
                      items: new OA\Items(ref: '#/components/schemas/EntrySummaryResource')
                    ),
                    new OA\Property(
                      property: 'Uncategorized',
                      type: 'array',
                      items: new OA\Items(ref: '#/components/schemas/EntrySummaryResource')
                    ),
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
  public function get($year = 'uncategorized'): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->entryRepository->getBySeason($year),
    ]);
  }
}
