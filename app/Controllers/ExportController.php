<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use App\Enums\ExportTypesEnum;
use App\Repositories\ExportRepository;
use App\Resources\DefaultResponse;

class ExportController extends Controller {

  private ExportRepository $exportRepository;

  public function __construct(ExportRepository $exportRepository) {
    $this->exportRepository = $exportRepository;
  }

  #[OA\Get(
    tags: ['Import'],
    path: '/api/exports',
    summary: 'Get All Exports',
    security: [['token' => [], 'api-key' => []]],
    responses: [
      new OA\Response(
        response: 200,
        description: 'OK',
        content: new OA\JsonContent(
          allOf: [
            new OA\Schema(ref: '#/components/schemas/DefaultSuccess'),
            new OA\Schema(
              properties: [
                new OA\Property(
                  property: 'data',
                  type: 'array',
                  items: new OA\Items(ref: '#/components/schemas/Export')
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
      'data' => $this->exportRepository->get_all(),
    ]);
  }

  #[OA\Get(
    tags: ['Import'],
    path: '/api/exports/{export_id}',
    summary: 'Get Single Export',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'export_id',
        description: 'Export UUID',
        in: 'path',
        required: true,
        example: 'e9597119-8452-4f2b-96d8-f2b1b1d2f158',
        schema: new OA\Schema(type: 'string', format: 'uuid')
      ),
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
                new OA\Property(
                  property: 'data',
                  type: 'string',
                  format: 'url',
                  example: 'https://download.url?expires=00000&signature=<signature>'
                ),
              ]
            ),
          ]
        )
      ),
      new OA\Response(response: 400, ref: '#/components/responses/ExportFileIncompleteResponse'),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function generate_download_url($uuid): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->exportRepository->get_download_url($uuid),
    ]);
  }

  #[OA\Get(
    tags: ['Import'],
    path: '/api/local/temp/{filename}',
    summary: 'Download Export File',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'filename',
        description: 'UUID',
        in: 'path',
        required: true,
        example: 'e9597119-8452-4f2b-96d8-f2b1b1d2f158',
        schema: new OA\Schema(type: 'string', format: 'uuid')
      ),
      new OA\Parameter(
        name: 'expires',
        in: 'query',
        required: true,
        schema: new OA\Schema(type: 'string')
      ),
      new OA\Parameter(
        name: 'signature',
        in: 'query',
        required: true,
        schema: new OA\Schema(type: 'string')
      ),
    ],
    responses: [
      new OA\Response(
        response: 200,
        description: 'OK',
        content: new OA\MediaType(
          mediaType: 'application/octet-stream',
          schema: new OA\Schema(type: 'string', format: 'binary')
        )
      ),
      new OA\Response(
        response: 400,
        ref: '#/components/responses/ExportFileIncompleteResponse'
      ),
      new OA\Response(
        response: 403,
        description: 'Forbidden',
        content: new OA\JsonContent(
          example: ['status' => 403, 'message' => 'Invalid signature provided'],
          properties: [
            new OA\Property(property: 'status', type: 'integer', format: 'int32'),
            new OA\Property(property: 'message', type: 'string'),
          ]
        )
      ),
      new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
      new OA\Response(response: 500, ref: '#/components/responses/ExportDownloadFailedResponse'),
    ]
  )]
  public function download(string $uuid): BinaryFileResponse {
    $data = $this->exportRepository->download($uuid);

    return response()->download($data['file'], $data['filename'], $data['headers']);
  }

  #[OA\Post(
    tags: ['Import'],
    path: '/api/exports/json',
    summary: 'Generate JSON Export',
    security: [['token' => [], 'api-key' => []]],
    responses: [
      new OA\Response(response: 200, ref: '#/components/responses/Success'),
      new OA\Response(response: 400, ref: '#/components/responses/ExportFileIncompleteResponse'),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function generate_json(): JsonResponse {
    ExportRepository::generate_export(ExportTypesEnum::JSON, false);

    return DefaultResponse::success();
  }

  #[OA\Post(
    tags: ['Import'],
    path: '/api/exports/sql',
    summary: 'Generate SQL Export',
    security: [['token' => [], 'api-key' => []]],
    responses: [
      new OA\Response(response: 200, ref: '#/components/responses/Success'),
      new OA\Response(response: 400, ref: '#/components/responses/ExportFileIncompleteResponse'),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function generate_sql(): JsonResponse {
    ExportRepository::generate_export(ExportTypesEnum::SQL, false);

    return DefaultResponse::success();
  }

  #[OA\Post(
    tags: ['Import'],
    path: '/api/exports/xlsx',
    summary: 'Generate XLSX (Excel File) Export',
    security: [['token' => [], 'api-key' => []]],
    responses: [
      new OA\Response(response: 200, ref: '#/components/responses/Success'),
      new OA\Response(response: 400, ref: '#/components/responses/ExportFileIncompleteResponse'),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function generate_xlsx(): JsonResponse {
    ExportRepository::generate_export(ExportTypesEnum::XLSX, false);

    return DefaultResponse::success();
  }
}

#[OA\Response(
  response: 'ExportDownloadFailedResponse',
  description: 'Other Error Responses',
  content: new OA\JsonContent(
    examples: [
      new OA\Examples(
        example: 'ZipFileProcessErrorExample',
        ref: '#/components/examples/ZipFileProcessErrorExample'
      ),
      new OA\Examples(
        example: 'BasicFailedExample',
        summary: 'Basic Failed Error',
        value: ['status' => 500, 'message' => 'Failed']
      ),
    ],
    properties: [
      new OA\Property(property: 'status', type: 'integer', format: 'int32'),
      new OA\Property(property: 'message', type: 'string'),
    ]
  )
)]
class ExportDownloadFailedResponse {
}
