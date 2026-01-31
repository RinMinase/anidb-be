<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

use App\Repositories\PCOwnerRepository;
use App\Requests\ImportRequest;
use App\Requests\PC\AddEditOwnerRequest;
use App\Requests\PC\GetOwnersRequest;
use App\Resources\DefaultResponse;

class PCOwnerController extends Controller {

  private PCOwnerRepository $pcOwnerRepository;

  public function __construct(PCOwnerRepository $pcOwnerRepository) {
    $this->pcOwnerRepository = $pcOwnerRepository;
  }

  #[OA\Get(
    path: "/api/pc/owners",
    summary: "Get All PC Owners",
    security: [["token" => [], "api-key" => []]],
    tags: ["PC"],
    responses: [
      new OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(
          allOf: [
            new OA\Schema(ref: "#/components/schemas/DefaultSuccess"),
            new OA\Schema(properties: [
              new OA\Property(property: "data", type: "array", items: new OA\Items(properties: [
                new OA\Property(property: "uuid", type: "string", format: "uuid", example: "e9597119-8452-4f2b-96d8-f2b1b1d2f158"),
                new OA\Property(property: "name", type: "string"),
                new OA\Property(
                  property: "infos",
                  type: "array",
                  items: new OA\Items(ref: "#/components/schemas/PCInfoSummaryResource")
                ),
              ])),
            ]),
          ]
        )
      ),
      new OA\Response(response: 401, ref: "#/components/responses/Unauthorized"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function index(GetOwnersRequest $request): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->pcOwnerRepository->getAll($request->only('show_hidden')),
    ]);
  }

  #[OA\Get(
    path: "/api/pc/owners/{owner_uuid}",
    summary: "Get PC Owner",
    security: [["token" => [], "api-key" => []]],
    tags: ["PC"],
    parameters: [
      new OA\Parameter(
        name: "owner_uuid",
        description: "Owner UUID",
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
              new OA\Property(property: "data", ref: "#/components/schemas/PCOwner"),
            ]),
          ]
        )
      ),
      new OA\Response(response: 401, ref: "#/components/responses/Unauthorized"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function get($uuid): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->pcOwnerRepository->get($uuid),
    ]);
  }

  #[OA\Post(
    path: "/api/pc/owners",
    summary: "Add a PC Owner",
    security: [["token" => [], "api-key" => []]],
    tags: ["PC"],
    parameters: [
      new OA\Parameter(ref: "#/components/parameters/pc_add_edit_owner_name")
    ],
    responses: [
      new OA\Response(response: 200, ref: "#/components/responses/Success"),
      new OA\Response(response: 401, ref: "#/components/responses/Unauthorized"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function add(AddEditOwnerRequest $request): JsonResponse {
    $this->pcOwnerRepository->add($request->only('name'));

    return DefaultResponse::success();
  }

  #[OA\Put(
    path: "/api/pc/owners/{owner_uuid}",
    summary: "Edit a PC Owner",
    security: [["token" => [], "api-key" => []]],
    tags: ["PC"],
    parameters: [
      new OA\Parameter(
        name: "owner_uuid",
        description: "Owner UUID",
        in: "path",
        required: true,
        example: "e9597119-8452-4f2b-96d8-f2b1b1d2f158",
        schema: new OA\Schema(type: "string", format: "uuid")
      ),
      new OA\Parameter(ref: "#/components/parameters/pc_add_edit_owner_name"),
    ],
    responses: [
      new OA\Response(response: 200, ref: "#/components/responses/Success"),
      new OA\Response(response: 401, ref: "#/components/responses/Unauthorized"),
      new OA\Response(response: 404, ref: "#/components/responses/NotFound"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function edit(AddEditOwnerRequest $request, $uuid): JsonResponse {
    $this->pcOwnerRepository->edit(
      $request->only('name'),
      $uuid
    );

    return DefaultResponse::success();
  }

  #[OA\Delete(
    path: "/api/pc/owners/{owner_uuid}",
    summary: "Delete a PC Owner",
    security: [["token" => [], "api-key" => []]],
    tags: ["PC"],
    parameters: [
      new OA\Parameter(name: "owner_uuid", description: "Owner UUID", in: "path", required: true, example: "e9597119-8452-4f2b-96d8-f2b1b1d2f158", schema: new OA\Schema(type: "string", format: "uuid"))
    ],
    responses: [
      new OA\Response(response: 200, ref: "#/components/responses/Success"),
      new OA\Response(response: 401, ref: "#/components/responses/Unauthorized"),
      new OA\Response(response: 404, ref: "#/components/responses/NotFound"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function delete($uuid): JsonResponse {
    $this->pcOwnerRepository->delete($uuid);

    return DefaultResponse::success();
  }

  #[OA\Post(
    path: "/api/pc/owners/import",
    summary: "Import a JSON file to add (does not delete existing) data for PC owners table",
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
    $count = $this->pcOwnerRepository->import($data);

    return DefaultResponse::success(null, [
      'data' => [
        'acceptedImports' => $count,
        'totalJsonEntries' => count($data),
      ],
    ]);
  }
}
