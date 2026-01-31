<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

use App\Repositories\PCInfoRepository;

use App\Requests\PC\AddEditInfoRequest;
use App\Requests\ImportRequest;

use App\Resources\DefaultResponse;

class PCInfoController extends Controller {

  private PCInfoRepository $pcInfoRepository;

  public function __construct(PCInfoRepository $pcInfoRepository) {
    $this->pcInfoRepository = $pcInfoRepository;
  }

  #[OA\Get(
    path: "/api/pc/infos/{info_uuid}",
    summary: "Get a PC Info",
    security: [["token" => [], "api-key" => []]],
    tags: ["PC"],
    parameters: [
      new OA\Parameter(
        name: "info_uuid",
        description: "PC Info UUID",
        in: "path",
        required: true,
        example: "e9597119-8452-4f2b-96d8-f2b1b1d2f158",
        schema: new OA\Schema(type: "string", format: "uuid")
      )
    ],
    responses: [
      new OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(
          allOf: [
            new OA\Schema(ref: "#/components/schemas/DefaultSuccess"),
            new OA\Schema(properties: [
              new OA\Property(property: "data", ref: "#/components/schemas/PCInfoResource"),
              new OA\Property(property: "stats", properties: [
                new OA\Property(property: "totalSetupCost", type: "integer", example: 10000),
                new OA\Property(property: "totalSetupCostFormat", type: "string", example: "10,000"),
                new OA\Property(property: "totalSystemCost", type: "integer", example: 10000),
                new OA\Property(property: "totalSystemCostFormat", type: "string", example: "10,000"),
                new OA\Property(property: "totalPeripheralCost", type: "integer", example: 10000),
                new OA\Property(property: "totalPeripheralCostFormat", type: "string", example: "10,000"),
                new OA\Property(property: "highlightCpu", type: "string", example: "Sample CPU Name"),
                new OA\Property(property: "highlightGpu", type: "string", example: "Sample GPU Name"),
                new OA\Property(property: "highlightRam", type: "string", example: "Sample RAM Details"),
                new OA\Property(property: "highlightStorage", type: "string", example: "Sample Storage Details"),
              ]),
            ]),
          ]
        )
      ),
      new OA\Response(response: 401, ref: "#/components/responses/Unauthorized"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function get($uuid): JsonResponse {
    $data = $this->pcInfoRepository->get($uuid);

    return DefaultResponse::success(null, [
      'data' => $data['data'],
      'stats' => $data['stats'],
    ]);
  }

  #[OA\Post(
    path: "/api/pc/infos",
    summary: "Add a PC Info",
    security: [["token" => [], "api-key" => []]],
    tags: ["PC"],
    parameters: [
      new OA\Parameter(ref: "#/components/parameters/pc_add_edit_info_id_owner"),
      new OA\Parameter(ref: "#/components/parameters/pc_add_edit_info_label"),
      new OA\Parameter(ref: "#/components/parameters/pc_add_edit_info_is_active"),
      new OA\Parameter(ref: "#/components/parameters/pc_add_edit_info_is_hidden"),
      new OA\Parameter(ref: "#/components/parameters/pc_add_edit_info_components"),
    ],
    responses: [
      new OA\Response(response: 200, ref: "#/components/responses/Success"),
      new OA\Response(response: 401, ref: "#/components/responses/Unauthorized"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function add(AddEditInfoRequest $request): JsonResponse {
    $this->pcInfoRepository->add(
      $request->only('id_owner', 'label', 'is_active', 'is_hidden', 'components'),
    );

    return DefaultResponse::success();
  }

  #[OA\Put(
    path: "/api/pc/infos/{info_uuid}",
    summary: "Edit a PC Info",
    security: [["token" => [], "api-key" => []]],
    tags: ["PC"],
    parameters: [
      new OA\Parameter(
        name: "info_uuid",
        description: "PC Info UUID",
        in: "path",
        required: true,
        example: "e9597119-8452-4f2b-96d8-f2b1b1d2f158",
        schema: new OA\Schema(type: "string", format: "uuid")
      ),
      new OA\Parameter(ref: "#/components/parameters/pc_add_edit_info_id_owner"),
      new OA\Parameter(ref: "#/components/parameters/pc_add_edit_info_label"),
      new OA\Parameter(ref: "#/components/parameters/pc_add_edit_info_is_active"),
      new OA\Parameter(ref: "#/components/parameters/pc_add_edit_info_is_hidden"),
      new OA\Parameter(ref: "#/components/parameters/pc_add_edit_info_components"),
    ],
    responses: [
      new OA\Response(response: 200, ref: "#/components/responses/Success"),
      new OA\Response(response: 401, ref: "#/components/responses/Unauthorized"),
      new OA\Response(response: 404, ref: "#/components/responses/NotFound"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function edit(AddEditInfoRequest $request, $uuid): JsonResponse {
    $this->pcInfoRepository->edit(
      $request->only('id_owner', 'label', 'is_active', 'is_hidden', 'components'),
      $uuid,
    );

    return DefaultResponse::success();
  }

  #[OA\Delete(
    path: "/api/pc/infos/{info_uuid}",
    summary: "Delete a PC Info",
    security: [["token" => [], "api-key" => []]],
    tags: ["PC"],
    parameters: [
      new OA\Parameter(
        name: "info_uuid",
        description: "PC Info UUID",
        in: "path",
        required: true,
        example: "e9597119-8452-4f2b-96d8-f2b1b1d2f158",
        schema: new OA\Schema(type: "string", format: "uuid")
      )
    ],
    responses: [
      new OA\Response(response: 200, ref: "#/components/responses/Success"),
      new OA\Response(response: 401, ref: "#/components/responses/Unauthorized"),
      new OA\Response(response: 404, ref: "#/components/responses/NotFound"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function delete($uuid): JsonResponse {
    $this->pcInfoRepository->delete($uuid);

    return DefaultResponse::success();
  }

  #[OA\Post(
    path: "/api/pc/infos/import",
    summary: "Import a JSON file to add (does not delete existing) data for PC infos table",
    security: [["token" => [], "api-key" => []]],
    tags: ["PC"],
    requestBody: new OA\RequestBody(
      required: true,
      content: new OA\MediaType(
        mediaType: "multipart/form-data",
        schema: new OA\Schema(
          properties: [
            new OA\Property(property: "file", type: "string", format: "binary")
          ]
        )
      )
    ),
    responses: [
      new OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(
          allOf: [
            new OA\Schema(ref: "#/components/schemas/DefaultSuccess"),
            new OA\Schema(
              properties: [
                new OA\Property(property: "data", ref: "#/components/schemas/DefaultImportSchema")
              ]
            ),
          ]
        )
      ),
      new OA\Response(response: 401, ref: "#/components/responses/Unauthorized"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function import(ImportRequest $request): JsonResponse {
    $file = $request->file('file')->get();

    if (!is_json($file)) {
      throw new JsonParsingException();
    }

    $data = json_decode($file);
    $count = $this->pcInfoRepository->import($data);

    return DefaultResponse::success(null, [
      'data' => [
        'acceptedImports' => $count,
        'totalJsonEntries' => count($data),
      ],
    ]);
  }

  #[OA\Post(
    path: "/api/pc/infos/{info_uuid}/duplicate",
    summary: "Duplicate a PC Info",
    security: [["token" => [], "api-key" => []]],
    tags: ["PC"],
    responses: [
      new OA\Response(response: 200, ref: "#/components/responses/Success"),
      new OA\Response(response: 401, ref: "#/components/responses/Unauthorized"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function duplicate($uuid): JsonResponse {
    $this->pcInfoRepository->duplicate($uuid);

    return DefaultResponse::success();
  }

  #[OA\Put(
    path: "/api/pc/infos/{info_uuid}/hide",
    summary: "Set a PC Info to either shown or hidden",
    security: [["token" => [], "api-key" => []]],
    tags: ["PC"],
    parameters: [
      new OA\Parameter(
        name: "info_uuid",
        description: "PC Info UUID",
        in: "path",
        required: true,
        example: "e9597119-8452-4f2b-96d8-f2b1b1d2f158",
        schema: new OA\Schema(type: "string", format: "uuid")
      )
    ],
    responses: [
      new OA\Response(response: 200, ref: "#/components/responses/Success"),
      new OA\Response(response: 401, ref: "#/components/responses/Unauthorized"),
      new OA\Response(response: 404, ref: "#/components/responses/NotFound"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function toggle_hide($uuid): JsonResponse {
    $this->pcInfoRepository->toggle_hide_setup($uuid);

    return DefaultResponse::success();
  }
}
