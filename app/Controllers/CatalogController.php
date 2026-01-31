<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

use App\Repositories\CatalogRepository;
use App\Requests\Catalog\AddEditRequest;
use App\Resources\DefaultResponse;

class CatalogController extends Controller {

  private CatalogRepository $catalogRepository;

  public function __construct(CatalogRepository $catalogRepository) {
    $this->catalogRepository = $catalogRepository;
  }

  #[OA\Get(
    tags: ['Catalog'],
    path: '/api/catalogs',
    summary: 'Get All Catalogs',
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
                      new OA\Property(property: 'uuid', type: 'string', format: 'uuid', example: 'e9597119-8452-4f2b-96d8-f2b1b1d2f158'),
                      new OA\Property(property: 'year', type: 'integer', format: 'int32', example: 2020),
                      new OA\Property(property: 'season', type: 'string', enum: ['Winter', 'Spring', 'Summer', 'Fall'], example: 'Winter'),
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
      'data' => $this->catalogRepository->getAll(),
    ]);
  }

  #[OA\Get(
    tags: ['Catalog'],
    path: '/api/catalogs/{catalog_id}/partials',
    summary: 'Get Partials in Catalog',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'catalog_id',
        description: 'Catalog ID',
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
                  type: 'array',
                  items: new OA\Items(ref: '#/components/schemas/PartialResource')
                ),
                new OA\Property(
                  property: 'stats',
                  properties: [
                    new OA\Property(
                      property: 'uuid',
                      type: 'string',
                      format: 'uuid',
                      example: 'e9597119-8452-4f2b-96d8-f2b1b1d2f158'
                    ),
                    new OA\Property(
                      property: 'year',
                      type: 'integer',
                      format: 'int32',
                      example: 2020
                    ),
                    new OA\Property(
                      property: 'season',
                      type: 'string',
                      enum: ['Winter', 'Spring', 'Summer', 'Fall'],
                      example: 'Winter'
                    ),
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
  public function get($uuid) {
    $data = $this->catalogRepository->get($uuid);

    return DefaultResponse::success(null, [
      'data' => $data['data'],
      'stats' => $data['stats'],
    ]);
  }

  #[OA\Post(
    tags: ['Catalog'],
    path: '/api/catalogs',
    summary: 'Add a Catalog',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(ref: '#/components/parameters/catalog_add_edit_season'),
      new OA\Parameter(ref: '#/components/parameters/catalog_add_edit_year'),
    ],
    responses: [
      new OA\Response(response: 200, ref: '#/components/responses/Success'),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function add(AddEditRequest $request): JsonResponse {
    $this->catalogRepository->add($request->only('season', 'year'));

    return DefaultResponse::success();
  }

  #[OA\Put(
    tags: ['Catalog'],
    path: '/api/catalogs/{catalog_id}',
    summary: 'Edit a Catalog',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'catalog_id',
        description: 'Catalog ID',
        in: 'path',
        required: true,
        example: 'e9597119-8452-4f2b-96d8-f2b1b1d2f158',
        schema: new OA\Schema(type: 'string', format: 'uuid')
      ),
      new OA\Parameter(ref: '#/components/parameters/catalog_add_edit_season'),
      new OA\Parameter(ref: '#/components/parameters/catalog_add_edit_year'),
    ],
    responses: [
      new OA\Response(response: 200, ref: '#/components/responses/Success'),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function edit(AddEditRequest $request, $id): JsonResponse {
    $this->catalogRepository->edit($request->only('season', 'year'), $id);

    return DefaultResponse::success();
  }

  #[OA\Delete(
    tags: ['Catalog'],
    path: '/api/catalogs/{catalog_id}',
    summary: 'Delete a Catalog',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'catalog_id',
        description: 'Catalog ID',
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
    $this->catalogRepository->delete($uuid);

    return DefaultResponse::success();
  }
}
