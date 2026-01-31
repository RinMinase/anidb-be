<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

use App\Repositories\PCComponentRepository;
use App\Requests\PC\AddEditComponentRequest;
use App\Requests\ImportRequest;
use App\Requests\PC\SearchComponentRequest;
use App\Resources\DefaultResponse;
use App\Resources\PC\PCComponentResource;

class PCComponentController extends Controller {

  private PCComponentRepository $pcComponentRepository;

  public function __construct(PCComponentRepository $pcComponentRepository) {
    $this->pcComponentRepository = $pcComponentRepository;
  }

  #[OA\Get(
    tags: ["PC"],
    path: "/api/pc/components",
    summary: "Get All PC Components",
    security: [["token" => []], ["api-key" => []]],
    parameters: [
      new OA\Parameter(ref: "#/components/parameters/pc_search_component_id_type"),
      new OA\Parameter(ref: "#/components/parameters/pc_search_component_limit"),
    ],
    responses: [
      new OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(
          allOf: [
            new OA\Schema(ref: "#/components/schemas/DefaultSuccess"),
            new OA\Schema(properties: [
              new OA\Property(
                property: "data",
                type: "array",
                items: new OA\Items(ref: "#/components/schemas/PCComponentResource")
              ),
            ]),
          ]
        )
      ),
      new OA\Response(response: 401, ref: "#/components/responses/Unauthorized"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function index(SearchComponentRequest $request): JsonResponse {
    $data = $this->pcComponentRepository->getAll(
      $request->only('id_type', 'limit'),
    );

    return DefaultResponse::success(null, [
      'data' => PCComponentResource::collection($data),
    ]);
  }

  #[OA\Get(
    tags: ["PC"],
    path: "/api/pc/components/{component_id}",
    summary: "Get a single PC Component",
    security: [["token" => []], ["api-key" => []]],
    parameters: [
      new OA\Parameter(
        name: "component_id",
        description: "Component ID",
        in: "path",
        required: true,
        example: 1,
        schema: new OA\Schema(type: "integer", format: "int32")
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
              new OA\Property(property: "data", ref: "#/components/schemas/PCComponentResource"),
            ]),
          ]
        )
      ),
      new OA\Response(response: 401, ref: "#/components/responses/Unauthorized"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function get($id): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => new PCComponentResource($this->pcComponentRepository->get($id)),
    ]);
  }

  #[OA\Post(
    tags: ["PC"],
    path: "/api/pc/components",
    summary: "Add a PC Component",
    security: [["token" => []], ["api-key" => []]],
    parameters: [
      new OA\Parameter(ref: "#/components/parameters/pc_add_edit_component_id_type"),
      new OA\Parameter(ref: "#/components/parameters/pc_add_edit_component_name"),
      new OA\Parameter(ref: "#/components/parameters/pc_add_edit_component_description"),
      new OA\Parameter(ref: "#/components/parameters/pc_add_edit_component_price"),
      new OA\Parameter(ref: "#/components/parameters/pc_add_edit_component_purchase_date"),
      new OA\Parameter(ref: "#/components/parameters/pc_add_edit_component_purchase_location"),
      new OA\Parameter(ref: "#/components/parameters/pc_add_edit_component_purchase_notes"),
      new OA\Parameter(ref: "#/components/parameters/pc_add_edit_component_is_onhand"),
    ],
    responses: [
      new OA\Response(response: 200, ref: "#/components/responses/Success"),
      new OA\Response(response: 401, ref: "#/components/responses/Unauthorized"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function add(AddEditComponentRequest $request): JsonResponse {
    $this->pcComponentRepository->add($request->only(
      'id_type',
      'name',
      'description',
      'price',
      'purchase_date',
      'purchase_location',
      'purchase_notes',
      'is_onhand',
    ));

    return DefaultResponse::success();
  }

  #[OA\Put(
    tags: ["PC"],
    path: "/api/pc/components/{component_id}",
    summary: "Edit a PC Component",
    security: [["token" => []], ["api-key" => []]],
    parameters: [
      new OA\Parameter(
        name: "component_id",
        description: "Component ID",
        in: "path",
        required: true,
        example: 1,
        schema: new OA\Schema(type: "integer", format: "int32")
      ),
      new OA\Parameter(ref: "#/components/parameters/pc_add_edit_component_id_type"),
      new OA\Parameter(ref: "#/components/parameters/pc_add_edit_component_name"),
      new OA\Parameter(ref: "#/components/parameters/pc_add_edit_component_description"),
      new OA\Parameter(ref: "#/components/parameters/pc_add_edit_component_price"),
      new OA\Parameter(ref: "#/components/parameters/pc_add_edit_component_purchase_date"),
      new OA\Parameter(ref: "#/components/parameters/pc_add_edit_component_purchase_location"),
      new OA\Parameter(ref: "#/components/parameters/pc_add_edit_component_purchase_notes"),
      new OA\Parameter(ref: "#/components/parameters/pc_add_edit_component_is_onhand"),
    ],
    responses: [
      new OA\Response(response: 200, ref: "#/components/responses/Success"),
      new OA\Response(response: 401, ref: "#/components/responses/Unauthorized"),
      new OA\Response(response: 404, ref: "#/components/responses/NotFound"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function edit(AddEditComponentRequest $request, $id): JsonResponse {
    $this->pcComponentRepository->edit(
      $request->only(
        'id_type',
        'name',
        'description',
        'price',
        'purchase_date',
        'purchase_location',
        'purchase_notes',
        'is_onhand'
      ),
      $id
    );

    return DefaultResponse::success();
  }

  #[OA\Delete(
    tags: ["PC"],
    path: "/api/pc/components/{component_id}",
    summary: "Delete a PC Component",
    security: [["token" => []], ["api-key" => []]],
    parameters: [
      new OA\Parameter(
        name: "component_id",
        description: "Component ID",
        in: "path",
        required: true,
        example: 1,
        schema: new OA\Schema(type: "integer", format: "int32")
      )
    ],
    responses: [
      new OA\Response(response: 200, ref: "#/components/responses/Success"),
      new OA\Response(response: 401, ref: "#/components/responses/Unauthorized"),
      new OA\Response(response: 404, ref: "#/components/responses/NotFound"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function delete($id): JsonResponse {
    $this->pcComponentRepository->delete($id);

    return DefaultResponse::success();
  }

  #[OA\Post(
    tags: ["PC"],
    path: "/api/pc/components/import",
    summary: "Import a JSON file to add (does not delete existing) data for PC components table",
    security: [["token" => []], ["api-key" => []]],
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
    $count = $this->pcComponentRepository->import($data);

    return DefaultResponse::success(null, [
      'data' => [
        'acceptedImports' => $count,
        'totalJsonEntries' => count($data),
      ],
    ]);
  }
}
