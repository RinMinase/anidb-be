<?php

namespace App\Controllers;

use TypeError;

use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

use App\Repositories\PartialRepository;

use App\Requests\Partial\AddEditMultipleRequest;
use App\Requests\Partial\AddEditRequest;

use App\Resources\DefaultResponse;

use App\Exceptions\Partial\ParsingException;
use App\Requests\Partial\GetAllRequest;

class PartialController extends Controller {

  private PartialRepository $partialRepository;

  public function __construct(PartialRepository $partialRepository) {
    $this->partialRepository = $partialRepository;
  }

  #[OA\Get(
    tags: ['Catalog'],
    path: '/api/partials',
    summary: 'Get All Partials in All Catalogs',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(ref: '#/components/parameters/partial_get_all_query'),
      new OA\Parameter(ref: '#/components/parameters/partial_get_all_column'),
      new OA\Parameter(ref: '#/components/parameters/partial_get_all_order'),
      new OA\Parameter(ref: '#/components/parameters/partial_get_all_page'),
      new OA\Parameter(ref: '#/components/parameters/partial_get_all_limit'),
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
                  items: new OA\Items(ref: '#/components/schemas/PartialWithCatalogResource')
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
  public function index(GetAllRequest $request) {
    $data = $this->partialRepository->get_all(
      $request->only('query', 'column', 'order', 'page', 'limit')
    );

    return DefaultResponse::success(null, [
      'data' => $data['data'],
      'meta' => $data['meta'],
    ]);
  }

  #[OA\Get(
    tags: ['Catalog'],
    path: '/api/partials/{partial_id}',
    summary: 'Get Partial Entry',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'partial_id',
        description: 'Partial ID',
        in: 'path',
        required: true,
        example: 'e9597119-8452-4f2b-96d8-f2b1b1d2f158',
        schema: new OA\Schema(type: 'string', format: 'uuid')
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
                  allOf: [
                    new OA\Schema(ref: '#/components/schemas/PartialResource'),
                    new OA\Schema(
                      properties: [
                        new OA\Property(
                          property: 'idPriority',
                          type: 'integer',
                          format: 'int32',
                          example: 1
                        ),
                        new OA\Property(
                          property: 'idCatalog',
                          type: 'string',
                          format: 'uuid',
                          example: 'e9597119-8452-4f2b-96d8-f2b1b1d2f158'
                        ),
                      ]
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
  public function get($uuid) {
    return DefaultResponse::success(null, [
      'data' => $this->partialRepository->get($uuid),
    ]);
  }

  #[OA\Post(
    tags: ['Catalog'],
    path: '/api/partials',
    summary: 'Add a Partial Entry',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(ref: '#/components/parameters/partial_add_edit_id_catalog'),
      new OA\Parameter(ref: '#/components/parameters/partial_add_edit_id_priority'),
      new OA\Parameter(ref: '#/components/parameters/partial_add_edit_title'),
    ],
    responses: [
      new OA\Response(response: 200, ref: '#/components/responses/Success'),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function add(AddEditRequest $request): JsonResponse {
    $this->partialRepository->add(
      $request->only('id_catalog', 'id_priority', 'title')
    );

    return DefaultResponse::success();
  }

  #[OA\Post(
    tags: ['Catalog'],
    path: '/api/partials/multi',
    summary: 'Multi Add a Partial Entry',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(ref: '#/components/parameters/partial_add_edit_multiple_data'),
      new OA\Parameter(ref: '#/components/parameters/partial_add_edit_multiple_season'),
      new OA\Parameter(ref: '#/components/parameters/partial_add_edit_multiple_year'),
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
                    new OA\Property(property: 'accepted', type: 'integer', format: 'int32', example: 0),
                    new OA\Property(property: 'total', type: 'integer', format: 'int32', example: 0),
                  ]
                ),
              ]
            ),
          ]
        )
      ),
      new OA\Response(response: 400, ref: '#/components/responses/PartialParsingResponse'),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function add_multiple(AddEditMultipleRequest $request): JsonResponse {
    try {
      $data = [];
      parse_str($request->get('data'), $data);

      if (!isset($data['low']) && !isset($data['normal']) && !isset($data['high'])) {
        throw new ParsingException();
      }

      $total_count = 0;

      if (isset($data['low'])) $total_count += count($data['low']);
      if (isset($data['normal'])) $total_count += count($data['normal']);
      if (isset($data['high'])) $total_count += count($data['high']);

      $count = $this->partialRepository->add_multiple([
        'data' => $data,
        'season' => $request->get('season'),
        'year' => $request->get('year'),
      ]);

      return DefaultResponse::success(null, [
        'data' => [
          'accepted' => $count,
          'total' => $total_count,
        ],
      ]);
    } catch (TypeError) {
      throw new ParsingException();
    }
  }

  #[OA\Put(
    tags: ['Catalog'],
    path: '/api/partials/{partial_id}',
    summary: 'Edit a Partial Entry',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'partial_id',
        in: 'path',
        required: true,
        example: 'e9597119-8452-4f2b-96d8-f2b1b1d2f158',
        schema: new OA\Schema(type: 'string', format: 'uuid')
      ),
      new OA\Parameter(ref: '#/components/parameters/partial_add_edit_id_catalog'),
      new OA\Parameter(ref: '#/components/parameters/partial_add_edit_id_priority'),
      new OA\Parameter(ref: '#/components/parameters/partial_add_edit_title'),
    ],
    responses: [
      new OA\Response(response: 200, ref: '#/components/responses/Success'),
      new OA\Response(response: 400, ref: '#/components/responses/BadRequest'),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function edit(AddEditRequest $request, $uuid): JsonResponse {
    $this->partialRepository->edit(
      $request->only('title', 'id_catalog', 'id_priority'),
      $uuid,
    );

    return DefaultResponse::success();
  }

  #[OA\Put(
    tags: ['Catalog'],
    path: '/api/partials/multi/{catalog_id}',
    summary: 'Multi Edit a Partial Entry',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'catalog_id',
        in: 'path',
        required: true,
        example: 'e9597119-8452-4f2b-96d8-f2b1b1d2f158',
        description: 'Catalog ID',
        schema: new OA\Schema(type: 'string', format: 'uuid')
      ),
      new OA\Parameter(ref: '#/components/parameters/partial_add_edit_multiple_data'),
      new OA\Parameter(ref: '#/components/parameters/partial_add_edit_multiple_season'),
      new OA\Parameter(ref: '#/components/parameters/partial_add_edit_multiple_year'),
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
                    new OA\Property(property: 'accepted', type: 'integer', format: 'int32', example: 0),
                    new OA\Property(property: 'total', type: 'integer', format: 'int32', example: 0),
                  ]
                ),
              ]
            ),
          ]
        )
      ),
      new OA\Response(response: 400, ref: '#/components/responses/PartialParsingResponse'),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function edit_multiple(AddEditMultipleRequest $request, $uuid): JsonResponse {
    try {
      $data = [];
      parse_str($request->get('data'), $data);

      if (!isset($data['low']) && !isset($data['normal']) && !isset($data['high'])) {
        throw new ParsingException();
      }

      $total_count = 0;

      if (isset($data['low'])) $total_count += count($data['low']);
      if (isset($data['normal'])) $total_count += count($data['normal']);
      if (isset($data['high'])) $total_count += count($data['high']);

      $count = $this->partialRepository->edit_multiple([
        'data' => $data,
        'season' => $request->get('season'),
        'year' => $request->get('year'),
      ], $uuid);

      return DefaultResponse::success(null, [
        'data' => [
          'accepted' => $count,
          'total' => $total_count,
        ],
      ]);
    } catch (TypeError) {
      throw new ParsingException();
    }
  }

  #[OA\Delete(
    tags: ['Catalog'],
    path: '/api/partials/{partial_id}',
    summary: 'Delete a Partial Entry',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'partial_id',
        in: 'path',
        required: true,
        example: 'e9597119-8452-4f2b-96d8-f2b1b1d2f158',
        schema: new OA\Schema(type: 'string', format: 'uuid')
      ),
    ],
    responses: [
      new OA\Response(response: 200, ref: '#/components/responses/Success'),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function delete($uuid): JsonResponse {
    $this->partialRepository->delete($uuid);

    return DefaultResponse::success();
  }
}
