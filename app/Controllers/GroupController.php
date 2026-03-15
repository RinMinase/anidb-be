<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

use App\Exceptions\JsonParsingException;
use App\Repositories\GroupRepository;
use App\Requests\ImportRequest;
use App\Resources\DefaultResponse;

class GroupController extends Controller {

  private GroupRepository $groupRepository;

  public function __construct(GroupRepository $groupRepository) {
    $this->groupRepository = $groupRepository;
  }

  #[OA\Get(
    tags: ['Group'],
    path: '/api/groups',
    summary: 'Get All Groups',
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
                  items: new OA\Items(ref: '#/components/schemas/Group')
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
      'data' => $this->groupRepository->getAll(),
    ]);
  }

  #[OA\Get(
    tags: ['Group'],
    path: '/api/groups/names',
    summary: 'Get All Group Names',
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
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(type: 'string')),
              ]
            ),
          ]
        )
      ),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function getNames(): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->groupRepository->getNames(),
    ]);
  }

  #[OA\Post(
    tags: ['Group'],
    path: '/api/groups',
    summary: 'Add a Group',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'name',
        in: 'query',
        required: true,
        example: 'Sample Group Name',
        schema: new OA\Schema(type: 'string', minLength: 1, maxLength: 64)
      ),
    ],
    responses: [
      new OA\Response(response: 200, ref: '#/components/responses/Success'),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function add(Request $request): JsonResponse {
    $values = $request->validate(['name' => ['required', 'string', 'max:64']]);

    $this->groupRepository->add($values);

    return DefaultResponse::success();
  }

  #[OA\Put(
    tags: ['Group'],
    path: '/api/groups/{group_id}',
    summary: 'Edit a Group',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'group_id',
        description: 'Group ID',
        in: 'path',
        required: true,
        example: 'e9597119-8452-4f2b-96d8-f2b1b1d2f158',
        schema: new OA\Schema(type: 'string', format: 'uuid')
      ),
      new OA\Parameter(
        name: 'name',
        in: 'query',
        required: true,
        example: 'Sample Group Name',
        schema: new OA\Schema(type: 'string', minLength: 1, maxLength: 64)
      ),
    ],
    responses: [
      new OA\Response(response: 200, ref: '#/components/responses/Success'),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function edit(Request $request, $uuid): JsonResponse {
    $values = $request->validate(['name' => ['required', 'string', 'max:64']]);

    $this->groupRepository->edit($values, $uuid);

    return DefaultResponse::success();
  }

  #[OA\Delete(
    tags: ['Group'],
    path: '/api/groups/{group_id}',
    summary: 'Delete a Group',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'group_id',
        description: 'Group ID',
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
    $this->groupRepository->delete($uuid);

    return DefaultResponse::success();
  }

  #[OA\Post(
    tags: ['Import - Archaic'],
    path: '/api/archaic/import/groups',
    summary: 'Import a JSON file to seed data for groups table',
    security: [['token' => [], 'api-key' => []]],
    requestBody: new OA\RequestBody(
      required: true,
      content: new OA\MediaType(
        mediaType: 'multipart/form-data',
        schema: new OA\Schema(
          properties: [
            new OA\Property(property: 'file', type: 'string', format: 'binary'),
          ]
        )
      )
    ),
    responses: [
      new OA\Response(
        response: 200,
        description: 'Success',
        content: new OA\JsonContent(
          allOf: [
            new OA\Schema(ref: '#/components/schemas/DefaultSuccess'),
            new OA\Schema(
              properties: [
                new OA\Property(property: 'data', ref: '#/components/schemas/DefaultImportSchema'),
              ]
            ),
          ]
        )
      ),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function import(ImportRequest $request) {
    $file = $request->file('file')->get();

    if (!is_json($file)) {
      throw new JsonParsingException();
    }

    $data = json_decode($file);
    $count = $this->groupRepository->import($data);

    return DefaultResponse::success(null, [
      'data' => [
        'acceptedImports' => $count,
        'totalJsonEntries' => count($data),
      ],
    ]);
  }
}
