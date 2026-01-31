<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

use App\Exceptions\JsonParsingException;
use App\Repositories\SequenceRepository;
use App\Requests\ImportRequest;
use App\Requests\Sequence\AddEditRequest;
use App\Resources\DefaultResponse;

class SequenceController extends Controller {

  private SequenceRepository $sequenceRepository;

  public function __construct(SequenceRepository $sequenceRepository) {
    $this->sequenceRepository = $sequenceRepository;
  }

  #[OA\Get(
    tags: ['Sequence'],
    path: '/api/sequences',
    summary: 'Get All Sequences',
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
                  items: new OA\Items(ref: '#/components/schemas/Sequence')
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
      'data' => $this->sequenceRepository->getAll(),
    ]);
  }

  #[OA\Get(
    tags: ['Sequence'],
    path: '/api/sequences/{sequence_id}',
    summary: 'Get Sequence',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'sequence_id',
        description: 'Sequence ID',
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
                new OA\Property(property: 'data', ref: '#/components/schemas/Sequence'),
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
    return DefaultResponse::success(null, [
      'data' => $this->sequenceRepository->get($id),
    ]);
  }

  #[OA\Post(
    tags: ['Sequence'],
    path: '/api/sequences',
    summary: 'Add a Sequence',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(ref: '#/components/parameters/sequence_add_edit_title'),
      new OA\Parameter(ref: '#/components/parameters/sequence_add_edit_date_from'),
      new OA\Parameter(ref: '#/components/parameters/sequence_add_edit_date_to'),
    ],
    responses: [
      new OA\Response(response: 200, ref: '#/components/responses/Success'),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function add(AddEditRequest $request): JsonResponse {
    $this->sequenceRepository->add(
      $request->only('title', 'date_from', 'date_to'),
    );

    return DefaultResponse::success();
  }

  #[OA\Put(
    tags: ['Sequence'],
    path: '/api/sequences/{sequence_id}',
    summary: 'Edit a Sequence',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'sequence_id',
        description: 'Sequence ID',
        in: 'path',
        required: true,
        example: 1,
        schema: new OA\Schema(type: 'integer', format: 'int32')
      ),
      new OA\Parameter(ref: '#/components/parameters/sequence_add_edit_title'),
      new OA\Parameter(ref: '#/components/parameters/sequence_add_edit_date_from'),
      new OA\Parameter(ref: '#/components/parameters/sequence_add_edit_date_to'),
    ],
    responses: [
      new OA\Response(response: 200, ref: '#/components/responses/Success'),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function edit(AddEditRequest $request, $id): JsonResponse {
    $this->sequenceRepository->edit(
      $request->only('title', 'date_from', 'date_to'),
      $id,
    );

    return DefaultResponse::success();
  }

  #[OA\Delete(
    tags: ['Sequence'],
    path: '/api/sequences/{sequence_id}',
    summary: 'Delete a Sequence',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'sequence_id',
        description: 'Sequence ID',
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
  public function delete($id): JsonResponse {
    $this->sequenceRepository->delete($id);

    return DefaultResponse::success();
  }

  #[OA\Post(
    tags: ['Import - Archaic'],
    path: '/api/archaic/import/sequences',
    summary: 'Import a JSON file to seed data for sequences table',
    security: [['token' => [], 'api-key' => []]],
    requestBody: new OA\RequestBody(
      required: true,
      content: new OA\MediaType(
        mediaType: 'multipart/form-data',
        schema: new OA\Schema(
          type: 'object',
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
  public function import(ImportRequest $request): JsonResponse {
    $file = $request->file('file')->get();

    if (!is_json($file)) {
      throw new JsonParsingException();
    }

    $data = json_decode($file);
    $count = $this->sequenceRepository->import($data);

    return DefaultResponse::success(null, [
      'data' => [
        'acceptedImports' => $count,
        'totalJsonEntries' => count($data),
      ],
    ]);
  }
}
