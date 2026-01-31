<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

use App\Exceptions\JsonParsingException;
use App\Repositories\ImportRepository;
use App\Requests\ImportRequest;
use App\Resources\DefaultResponse;

class ImportController extends Controller {

  private ImportRepository $importRepository;

  public function __construct(ImportRepository $importRepository) {
    $this->importRepository = $importRepository;
  }

  #[OA\Schema(
    schema: 'ImportDataCount',
    title: 'Import Accepted & Total Schema',
    properties: [
      new OA\Property(property: 'accepted', type: 'integer', format: 'int32', example: 0),
      new OA\Property(property: 'total', type: 'integer', format: 'int32', example: 0),
    ]
  )]

  #[OA\Post(
    tags: ['Import - Archaic'],
    path: '/api/archaic/import',
    summary: 'Import a JSON file to seed data for all tables',
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
                new OA\Property(
                  property: 'data',
                  properties: [
                    new OA\Property(property: 'entries', ref: '#/components/schemas/ImportDataCount'),
                    new OA\Property(property: 'buckets', ref: '#/components/schemas/ImportDataCount'),
                    new OA\Property(property: 'sequences', ref: '#/components/schemas/ImportDataCount'),
                    new OA\Property(property: 'groups', ref: '#/components/schemas/ImportDataCount'),
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
  public function import_archaic_format(ImportRequest $request): JsonResponse {
    $file = $request->file('file')->get();

    if (!is_json($file)) {
      throw new JsonParsingException();
    }

    $data = json_decode($file);
    $import_count = $this->importRepository->import($data);

    $entry_count = 0;
    if (!empty($data->entry)) {
      $entry_count = count($data->entry);
    }

    $bucket_count = 0;
    if (!empty($data->bucket)) {
      $bucket_count = count($data->bucket);
    }

    $sequence_count = 0;
    if (!empty($data->sequence)) {
      $sequence_count = count($data->sequence);
    }

    $group_count = 0;
    if (!empty($data->group)) {
      $group_count = count($data->group);
    }

    return DefaultResponse::success(null, [
      'data' => [
        'entries' => [
          'accepted' => $import_count['entry'],
          'total' => $entry_count,
        ],
        'buckets' => [
          'accepted' => $import_count['bucket'],
          'total' => $bucket_count,
        ],
        'sequences' => [
          'accepted' => $import_count['sequence'],
          'total' => $sequence_count,
        ],
        'groups' => [
          'accepted' => $import_count['group'],
          'total' => $group_count,
        ],
      ],
    ]);
  }
}
