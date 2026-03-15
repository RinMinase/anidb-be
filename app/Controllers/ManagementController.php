<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

use App\Repositories\ManagementRepository;
use App\Resources\DefaultResponse;

class ManagementController extends Controller {

  private ManagementRepository $managementRepository;

  public function __construct(ManagementRepository $managementRepository) {
    $this->managementRepository = $managementRepository;
  }

  #[OA\Get(
    tags: ['Management'],
    path: '/api/management',
    summary: 'Get Management Information',
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
                  ref: '#/components/schemas/ManagementSchema'
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
      'data' => $this->managementRepository->index(),
    ]);
  }

  #[OA\Get(
    tags: ['Management'],
    path: '/api/management/by-year',
    summary: 'Get Titles Watched per Month of Year',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: "year",
        in: "query",
        example: "2020",
        schema: new OA\Schema(ref: "#/components/schemas/YearSchema")
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
                  ref: '#/components/schemas/ManagementSchema'
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
  public function get_by_year(Request $request): JsonResponse {
    $values = $request->validate(['year' => ['nullable', new YearRule]]);

    return DefaultResponse::success(null, [
      'data' => $this->managementRepository->get_by_year($values),
    ]);
  }
}

#[OA\Schema(
  properties: [
    new OA\Property(
      property: 'count',
      properties: [
        new OA\Property(property: 'entries', type: 'integer', format: 'int32', description: 'Total Entries', example: 0),
        new OA\Property(property: 'buckets', type: 'integer', format: 'int32', description: 'Total Buckets', example: 0),
        new OA\Property(property: 'partials', type: 'integer', format: 'int32', description: 'Total Partials', example: 0),
      ]
    ),
    new OA\Property(
      property: 'stats',
      properties: [
        new OA\Property(property: 'watchSeconds', type: 'integer', format: 'int64', description: 'Watch time in seconds', example: 0),
        new OA\Property(property: 'watch', type: 'string', description: 'Watch time in days', example: '10 days'),
        new OA\Property(property: 'watchSubtext', type: 'string', description: 'Watch time subtext', example: '10 hours, 10 minutes, 10 seconds'),
        new OA\Property(property: 'rewatchSeconds', type: 'integer', format: 'int64', description: 'Watch with Rewatch time in seconds', example: 0),
        new OA\Property(property: 'rewatch', type: 'string', description: 'Watch with Rewatch time in days', example: '10 days'),
        new OA\Property(property: 'rewatchSubtext', type: 'string', description: 'Watch with Rewatch time subtext', example: '10 hours, 10 minutes, 10 seconds'),
        new OA\Property(property: 'bucketSize', type: 'string', description: 'Total Buckets size', example: '0 TB'),
        new OA\Property(property: 'entrySize', type: 'string', description: 'Total Entries size', example: '0 TB'),
        new OA\Property(property: 'episodes', type: 'integer', format: 'int64', description: 'Total episode count', example: 0),
        new OA\Property(property: 'titles', type: 'integer', format: 'int64', description: 'Total title count', example: 0),
        new OA\Property(property: 'seasons', type: 'integer', format: 'int64', description: 'Total season count', example: 0),
      ]
    ),
    new OA\Property(
      property: 'graph',
      properties: [
        new OA\Property(
          property: 'quality',
          description: 'Titles watched per quality',
          properties: [
            new OA\Property(property: 'quality2160', type: 'integer', format: 'int32', example: 0),
            new OA\Property(property: 'quality1080', type: 'integer', format: 'int32', example: 0),
            new OA\Property(property: 'quality720', type: 'integer', format: 'int32', example: 0),
            new OA\Property(property: 'quality480', type: 'integer', format: 'int32', example: 0),
            new OA\Property(property: 'quality360', type: 'integer', format: 'int32', example: 0),
          ]
        ),
        new OA\Property(
          property: 'ratings',
          description: 'Titles watched per rating -- rating values per index',
          type: 'array',
          items: new OA\Items(type: 'integer', format: 'int32', example: 10)
        ),
        new OA\Property(
          property: 'months',
          description: 'Titles watched per month',
          properties: [
            new OA\Property(property: 'jan', type: 'integer', format: 'int32', example: 0),
            new OA\Property(property: 'feb', type: 'integer', format: 'int32', example: 0),
            new OA\Property(property: 'mar', type: 'integer', format: 'int32', example: 0),
            new OA\Property(property: 'apr', type: 'integer', format: 'int32', example: 0),
            new OA\Property(property: 'may', type: 'integer', format: 'int32', example: 0),
            new OA\Property(property: 'jun', type: 'integer', format: 'int32', example: 0),
            new OA\Property(property: 'jul', type: 'integer', format: 'int32', example: 0),
            new OA\Property(property: 'aug', type: 'integer', format: 'int32', example: 0),
            new OA\Property(property: 'sep', type: 'integer', format: 'int32', example: 0),
            new OA\Property(property: 'oct', type: 'integer', format: 'int32', example: 0),
            new OA\Property(property: 'nov', type: 'integer', format: 'int32', example: 0),
            new OA\Property(property: 'dec', type: 'integer', format: 'int32', example: 0),
          ]
        ),
        new OA\Property(
          property: 'years',
          description: 'Titles watched per year',
          type: 'array',
          items: new OA\Items(
            properties: [
              new OA\Property(property: 'year', type: 'string', example: '2010'),
              new OA\Property(property: 'value', type: 'integer', example: 10),
            ]
          )
        ),
        new OA\Property(
          property: 'seasons',
          description: 'Titles watched per season',
          type: 'array',
          items: new OA\Items(
            properties: [
              new OA\Property(property: 'season', type: 'string', example: 'Spring'),
              new OA\Property(property: 'value', type: 'integer', example: 10),
            ]
          )
        ),
        new OA\Property(
          property: 'genres',
          description: 'Titles watched per genre',
          properties: [
            new OA\Property(
              property: 'list',
              type: 'array',
              items: new OA\Items(type: 'string', example: 'Action')
            ),
            new OA\Property(
              property: 'values',
              type: 'array',
              items: new OA\Items(
                properties: [
                  new OA\Property(property: 'genre', type: 'string', example: 'Action'),
                  new OA\Property(property: 'value', type: 'integer', example: 10),
                ]
              )
            ),
          ]
        ),
      ]
    ),
  ]
)]
class ManagementSchema {
}
