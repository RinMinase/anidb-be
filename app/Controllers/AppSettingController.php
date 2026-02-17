<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

use App\Repositories\AppSettingRepository;
use App\Requests\AppSetting\AddEditRequest;
use App\Resources\DefaultResponse;

class AppSettingController extends Controller {

  private AppSettingRepository $appSettingRepository;

  public function __construct(AppSettingRepository $appSettingRepository) {
    $this->appSettingRepository = $appSettingRepository;
  }

  #[OA\Get(
    tags: ['App Settings'],
    path: '/api/app-settings',
    summary: 'Get All Application Settings',
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
                  items: new OA\Items(ref: '#/components/schemas/AppSetting')
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
      'data' => $this->appSettingRepository->getAll(),
    ]);
  }

  #[OA\Get(
    tags: ['App Settings'],
    path: '/api/app-settings/{settings_id}',
    summary: 'Get an Application Setting',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'settings_id',
        description: 'Settings ID',
        in: 'path',
        required: true,
        example: 1,
        schema: new OA\Schema(type: 'integer', format: 'int64')
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
                  ref: '#/components/schemas/AppSetting'
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
  public function get($id): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->appSettingRepository->get($id),
    ]);
  }

  #[OA\Post(
    tags: ['App Settings'],
    path: '/api/app-settings',
    summary: 'Add an Application Setting',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(ref: '#/components/parameters/app_setting_add_edit_key'),
      new OA\Parameter(ref: '#/components/parameters/app_setting_add_edit_value'),
    ],
    responses: [
      new OA\Response(response: 200, ref: '#/components/responses/Success'),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function add(AddEditRequest $request): JsonResponse {
    $this->appSettingRepository->add($request->only('key', 'value'));

    return DefaultResponse::success();
  }

  #[OA\Put(
    tags: ['App Settings'],
    path: '/api/app-settings/{settings_id}',
    summary: 'Edit an Application Setting',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'settings_id',
        description: 'Settings ID',
        in: 'path',
        required: true,
        example: 1,
        schema: new OA\Schema(type: 'integer', format: 'int64')
      ),
      new OA\Parameter(ref: '#/components/parameters/app_setting_add_edit_key'),
      new OA\Parameter(ref: '#/components/parameters/app_setting_add_edit_value'),
    ],
    responses: [
      new OA\Response(response: 200, ref: '#/components/responses/Success'),
      new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
      new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
      new OA\Response(response: 500, ref: '#/components/responses/Failed'),
    ]
  )]
  public function edit(AddEditRequest $request, $id): JsonResponse {
    $this->appSettingRepository->edit($request->only('key', 'value'), $id);

    return DefaultResponse::success();
  }

  #[OA\Delete(
    tags: ['App Settings'],
    path: '/api/app-settings/{settings_id}',
    summary: 'Deleten Application a Setting',
    security: [['token' => [], 'api-key' => []]],
    parameters: [
      new OA\Parameter(
        name: 'settings_id',
        description: 'Settings ID',
        in: 'path',
        required: true,
        example: 1,
        schema: new OA\Schema(type: 'integer', format: 'int64')
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
    $this->appSettingRepository->delete($id);

    return DefaultResponse::success();
  }
}
