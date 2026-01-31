<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

use App\Repositories\PCComponentTypeRepository;
use App\Requests\PC\AddEditComponentTypeRequest;
use App\Resources\DefaultResponse;

class PCComponentTypeController extends Controller {

  private PCComponentTypeRepository $pcComponentTypeRepository;

  public function __construct(PCComponentTypeRepository $pcComponentTypeRepository) {
    $this->pcComponentTypeRepository = $pcComponentTypeRepository;
  }

  #[OA\Get(
    tags: ['PC'],
    path: '/api/pc/types',
    summary: 'Get All PC Component Types',
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
                  items: new OA\Items(ref: '#/components/schemas/PCComponentType')
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
      'data' => $this->pcComponentTypeRepository->getAll(),
    ]);
  }

  #[OA\Post(
    tags: ['PC'],
    path: '/api/pc/types',
    summary: 'Add a PC Component Type',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(ref: '#/components/parameters/pc_add_edit_component_type_type'),
      new OA\Parameter(ref: '#/components/parameters/pc_add_edit_component_type_name'),
      new OA\Parameter(ref: '#/components/parameters/pc_add_edit_component_type_is_peripheral'),
    ],
    responses: [
      new OA\Response(response: 200, ref: '#/components/responses/Success'),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function add(AddEditComponentTypeRequest $request): JsonResponse {
    $this->pcComponentTypeRepository->add($request->only('type', 'name', 'is_peripheral'));

    return DefaultResponse::success();
  }

  #[OA\Put(
    tags: ['PC'],
    path: '/api/pc/types/{type_id}',
    summary: 'Edit a PC Component Type',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'type_id',
        description: 'Type ID',
        in: 'path',
        required: true,
        example: 1,
        schema: new OA\Schema(type: 'integer', format: 'int32')
      ),
      new OA\Parameter(ref: '#/components/parameters/pc_add_edit_component_type_type'),
      new OA\Parameter(ref: '#/components/parameters/pc_add_edit_component_type_name'),
      new OA\Parameter(ref: '#/components/parameters/pc_add_edit_component_type_is_peripheral'),
    ],
    responses: [
      new OA\Response(response: 200, ref: '#/components/responses/Success'),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function edit(AddEditComponentTypeRequest $request, $id): JsonResponse {
    $this->pcComponentTypeRepository->edit($request->only('type', 'name', 'is_peripheral'), $id);

    return DefaultResponse::success();
  }

  #[OA\Delete(
    tags: ['PC'],
    path: '/api/pc/types/{type_id}',
    summary: 'Delete a PC Component Type',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'type_id',
        description: 'Type ID',
        in: 'path',
        required: true,
        example: 1,
        schema: new OA\Schema(type: 'integer', format: 'int32')
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
    $this->pcComponentTypeRepository->delete($uuid);

    return DefaultResponse::success();
  }
}
