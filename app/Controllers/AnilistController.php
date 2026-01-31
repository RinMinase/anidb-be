<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

use App\Repositories\AnilistRepository;

use App\Resources\Anilist\AnilistSearchResource;
use App\Resources\Anilist\AnilistTitleResource;
use App\Resources\DefaultResponse;

class AnilistController extends Controller {

  private AnilistRepository $anilistRepository;

  public function __construct(AnilistRepository $anilistRepository) {
    $this->anilistRepository = $anilistRepository;
  }

  #[OA\Get(
    path: "/api/anilist/title/{title_id}",
    summary: "Retrieve Title Information",
    security: [["token" => []], ["api-key" => []]],
    tags: ["AniList"],
    parameters: [
      new OA\Parameter(
        name: "title_id",
        description: "Title ID",
        in: "path",
        required: true,
        example: "101280",
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
              new OA\Property(property: "data", ref: "#/components/schemas/AnilistTitleResource"),
            ]),
          ]
        )
      ),
      new OA\Response(response: 401, ref: "#/components/responses/Unauthorized"),
      new OA\Response(response: 429, ref: "#/components/responses/AnilistRateLimitErrorResponse"),
      new OA\Response(response: 500, ref: "#/components/responses/AnilistOtherErrorResponse"),
      new OA\Response(response: 503, ref: "#/components/responses/AnilistConnectionErrorResponse"),
    ]
  )]
  public function get($id = 101280): JsonResponse {
    $data = $this->anilistRepository->get($id);

    $data = $data['Media'];

    return DefaultResponse::success(null, [
      'data' => new AnilistTitleResource($data),
    ]);
  }

  #[OA\Get(
    path: "/api/anilist/search",
    summary: "Query Titles",
    security: [["token" => []], ["api-key" => []]],
    tags: ["AniList"],
    parameters: [
      new OA\Parameter(
        name: "query",
        description: "Title Search String",
        in: "query",
        required: true,
        example: "tensei",
        schema: new OA\Schema(type: "string")
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
              new OA\Property(
                property: "data",
                type: "array",
                items: new OA\Items(ref: "#/components/schemas/AnilistSearchResource")
              ),
            ]),
          ]
        )
      ),
      new OA\Response(response: 401, ref: "#/components/responses/Unauthorized"),
      new OA\Response(response: 429, ref: "#/components/responses/AnilistRateLimitErrorResponse"),
      new OA\Response(response: 500, ref: "#/components/responses/AnilistOtherErrorResponse"),
      new OA\Response(response: 503, ref: "#/components/responses/AnilistConnectionErrorResponse"),
    ]
  )]
  public function search(Request $request): JsonResponse {
    $data = $this->anilistRepository->search($request->only('query'));
    $data = collect($data['Page']['media']);

    return DefaultResponse::success(null, [
      'data' => AnilistSearchResource::collection($data),
    ]);
  }
}

#[OA\Response(
  response: "AnilistOtherErrorResponse",
  description: "Other Error Responses",
  content: new OA\JsonContent(
    properties: [
      new OA\Property(property: "status", type: "integer", format: "int32"),
      new OA\Property(property: "message", type: "string"),
    ],
    examples: [
      new OA\Examples(example: "AnilistConfigErrorExample", ref: "#/components/examples/AnilistConfigErrorExample"),
      new OA\Examples(example: "AnilistParsingErrorExample", ref: "#/components/examples/AnilistParsingErrorExample"),
    ]
  )
)]
class AnilistOtherErrorResponse {
}
