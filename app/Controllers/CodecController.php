<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

use App\Repositories\CodecRepository;

use App\Requests\Codec\AddEditRequest;

use App\Resources\DefaultResponse;

class CodecController extends Controller {

  private CodecRepository $codecRepository;

  public function __construct(CodecRepository $codecRepository) {
    $this->codecRepository = $codecRepository;
  }

  #[OA\Get(
    tags: ["Codec"],
    path: "/api/codecs",
    summary: "Get All Codecs",
    security: [["token" => [], "api-key" => []]],
    responses: [
      new OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(
          allOf: [
            new OA\Schema(ref: "#/components/schemas/DefaultSuccess"),
            new OA\Schema(
              properties: [
                new OA\Property(property: "data", properties: [
                  new OA\Property(
                    property: "audio",
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/CodecAudio")
                  ),
                  new OA\Property(
                    property: "video",
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/CodecVideo")
                  ),
                ]),
              ]
            ),
          ]
        )
      ),
      new OA\Response(response: 401, ref: "#/components/responses/Unauthorized"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function index(): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->codecRepository->getAll(),
    ]);
  }

  #[OA\Get(
    tags: ["Codec"],
    path: "/api/codecs/audio",
    summary: "Get All Audio Codecs",
    security: [["token" => [], "api-key" => []]],
    responses: [
      new OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(
          allOf: [
            new OA\Schema(ref: "#/components/schemas/DefaultSuccess"),
            new OA\Schema(
              properties: [
                new OA\Property(
                  property: "data",
                  type: "array",
                  items: new OA\Items(ref: "#/components/schemas/CodecAudio")
                ),
              ]
            ),
          ]
        )
      ),
      new OA\Response(response: 401, ref: "#/components/responses/Unauthorized"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function getAudio(): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->codecRepository->getAudio(),
    ]);
  }

  #[OA\Post(
    tags: ["Codec"],
    path: "/api/codecs/audio",
    summary: "Add an Audio Codec",
    security: [["token" => [], "api-key" => []]],
    parameters: [
      new OA\Parameter(ref: "#/components/parameters/codec_add_edit_codec"),
      new OA\Parameter(ref: "#/components/parameters/codec_add_edit_order"),
    ],
    responses: [
      new OA\Response(response: 200, ref: "#/components/responses/Success"),
      new OA\Response(response: 401, ref: "#/components/responses/Unauthorized"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function addAudio(AddEditRequest $request): JsonResponse {
    $this->codecRepository->addAudio($request->only('codec', 'order'));

    return DefaultResponse::success();
  }

  #[OA\Put(
    tags: ["Codec"],
    path: "/api/codecs/audio/{audio_codec_id}",
    summary: "Edit an Audio Codec",
    security: [["token" => [], "api-key" => []]],
    parameters: [
      new OA\Parameter(
        name: "audio_codec_id",
        description: "Audio Codec ID",
        in: "path",
        required: true,
        example: "1",
        schema: new OA\Schema(type: "integer", format: "int64")
      ),
      new OA\Parameter(ref: "#/components/parameters/codec_add_edit_codec"),
      new OA\Parameter(ref: "#/components/parameters/codec_add_edit_order"),
    ],
    responses: [
      new OA\Response(response: 200, ref: "#/components/responses/Success"),
      new OA\Response(response: 401, ref: "#/components/responses/Unauthorized"),
      new OA\Response(response: 404, ref: "#/components/responses/NotFound"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function editAudio(AddEditRequest $request, $id): JsonResponse {
    $this->codecRepository->editAudio($request->only('codec', 'order'), $id);

    return DefaultResponse::success();
  }

  #[OA\Delete(
    tags: ["Codec"],
    path: "/api/codecs/audio/{audio_codec_id}",
    summary: "Delete an Audio Codec",
    security: [["token" => [], "api-key" => []]],
    parameters: [
      new OA\Parameter(
        name: "audio_codec_id",
        description: "Audio Codec ID",
        in: "path",
        required: true,
        example: "1",
        schema: new OA\Schema(type: "integer", format: "int64")
      ),
    ],
    responses: [
      new OA\Response(response: 200, ref: "#/components/responses/Success"),
      new OA\Response(response: 401, ref: "#/components/responses/Unauthorized"),
      new OA\Response(response: 404, ref: "#/components/responses/NotFound"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function deleteAudio($id): JsonResponse {
    $this->codecRepository->deleteAudio($id);

    return DefaultResponse::success();
  }

  #[OA\Get(
    tags: ["Codec"],
    path: "/api/codecs/video",
    summary: "Get All Video Codecs",
    security: [["token" => [], "api-key" => []]],
    responses: [
      new OA\Response(
        response: 200,
        description: "Success",
        content: new OA\JsonContent(
          allOf: [
            new OA\Schema(ref: "#/components/schemas/DefaultSuccess"),
            new OA\Schema(
              properties: [
                new OA\Property(
                  property: "data",
                  type: "array",
                  items: new OA\Items(ref: "#/components/schemas/CodecVideo")
                ),
              ]
            ),
          ]
        )
      ),
      new OA\Response(response: 401, ref: "#/components/responses/Unauthorized"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function getVideo(): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->codecRepository->getVideo(),
    ]);
  }

  #[OA\Post(
    tags: ["Codec"],
    path: "/api/codecs/video",
    summary: "Add a Video Codec",
    security: [["token" => [], "api-key" => []]],
    parameters: [
      new OA\Parameter(ref: "#/components/parameters/codec_add_edit_codec"),
      new OA\Parameter(ref: "#/components/parameters/codec_add_edit_order"),
    ],
    responses: [
      new OA\Response(response: 200, ref: "#/components/responses/Success"),
      new OA\Response(response: 401, ref: "#/components/responses/Unauthorized"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function addVideo(AddEditRequest $request): JsonResponse {
    $this->codecRepository->addVideo($request->only('codec', 'order'));

    return DefaultResponse::success();
  }

  #[OA\Put(
    tags: ["Codec"],
    path: "/api/codecs/video/{video_codec_id}",
    summary: "Edit a Video Codec",
    security: [["token" => [], "api-key" => []]],
    parameters: [
      new OA\Parameter(
        name: "video_codec_id",
        description: "Video Codec ID",
        in: "path",
        required: true,
        example: "1",
        schema: new OA\Schema(type: "integer", format: "int64")
      ),
      new OA\Parameter(ref: "#/components/parameters/codec_add_edit_codec"),
      new OA\Parameter(ref: "#/components/parameters/codec_add_edit_order"),
    ],
    responses: [
      new OA\Response(response: 200, ref: "#/components/responses/Success"),
      new OA\Response(response: 401, ref: "#/components/responses/Unauthorized"),
      new OA\Response(response: 404, ref: "#/components/responses/NotFound"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function editVideo(AddEditRequest $request, $id): JsonResponse {
    $this->codecRepository->editVideo($request->only('codec', 'order'), $id);

    return DefaultResponse::success();
  }

  #[OA\Delete(
    tags: ["Codec"],
    path: "/api/codecs/video/{video_codec_id}",
    summary: "Delete an Video Codec",
    security: [["token" => [], "api-key" => []]],
    parameters: [
      new OA\Parameter(
        name: "video_codec_id",
        description: "Video Codec ID",
        in: "path",
        required: true,
        example: "1",
        schema: new OA\Schema(type: "integer", format: "int64")
      ),
    ],
    responses: [
      new OA\Response(response: 200, ref: "#/components/responses/Success"),
      new OA\Response(response: 401, ref: "#/components/responses/Unauthorized"),
      new OA\Response(response: 404, ref: "#/components/responses/NotFound"),
      new OA\Response(response: 500, ref: "#/components/responses/Failed"),
    ]
  )]
  public function deleteVideo($id): JsonResponse {
    $this->codecRepository->deleteVideo($id);

    return DefaultResponse::success();
  }
}
