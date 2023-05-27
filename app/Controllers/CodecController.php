<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Repositories\CodecRepository;

use App\Resources\DefaultResponse;

class CodecController extends Controller {

  private CodecRepository $codecRepository;

  public function __construct(CodecRepository $codecRepository) {
    $this->codecRepository = $codecRepository;
  }

  /**
   * @OA\Get(
   *   tags={"Codec"},
   *   path="/api/codecs",
   *   summary="Get All Codecs",
   *   security={{"token":{}}},
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       @OA\Property(
   *         property="data",
   *         @OA\Property(
   *           property="audio",
   *           type="array",
   *           @OA\Items(ref="#/components/schemas/CodecAudio"),
   *         ),
   *         @OA\Property(
   *           property="video",
   *           type="array",
   *           @OA\Items(ref="#/components/schemas/CodecVideo"),
   *         ),
   *       ),
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function index(): JsonResponse {
    return response()->json([
      'data' => $this->codecRepository->getAll(),
    ]);
  }

  /**
   * @OA\Get(
   *   tags={"Codec"},
   *   path="/api/codecs/audio",
   *   summary="Get All Audio Codecs",
   *   security={{"token":{}}},
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       @OA\Property(
   *         property="data",
   *         type="array",
   *         @OA\Items(ref="#/components/schemas/CodecAudio"),
   *       ),
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function getAudio(): JsonResponse {
    return response()->json([
      'data' => $this->codecRepository->getAudio(),
    ]);
  }

  /**
   * @OA\Post(
   *   tags={"Codec"},
   *   path="/api/codecs/audio",
   *   summary="Add an Audio Codec",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="codec",
   *     in="query",
   *     required=true,
   *     example="Sample Codec",
   *     @OA\Schema(type="string"),
   *   ),
   *   @OA\Parameter(
   *     name="order",
   *     in="query",
   *     example="1",
   *     @OA\Schema(type="integer", format="int32"),
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function addAudio(Request $request): JsonResponse {
    $this->codecRepository->addAudio($request->all());

    return DefaultResponse::success();
  }

  /**
   * @OA\Put(
   *   tags={"Codec"},
   *   path="/api/codecs/audio/{audio_codec_id}",
   *   summary="Edit an Audio Codec",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="audio_codec_id",
   *     description="Audio Codec ID",
   *     in="path",
   *     required=true,
   *     example="1",
   *     @OA\Schema(type="integer", format="int64"),
   *   ),
   *   @OA\Parameter(
   *     name="codec",
   *     in="query",
   *     required=true,
   *     example="Sample Codec",
   *     @OA\Schema(type="string"),
   *   ),
   *   @OA\Parameter(
   *     name="order",
   *     in="query",
   *     example="1",
   *     @OA\Schema(type="integer", format="int32"),
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function editAudio(Request $request, $id): JsonResponse {
    $this->codecRepository->editAudio($request->except(['_method']), $id);

    return DefaultResponse::success();
  }

  /**
   * @OA\Delete(
   *   tags={"Codec"},
   *   path="/api/codecs/audio/{audio_codec_id}",
   *   summary="Delete an Audio Codec",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="audio_codec_id",
   *     description="Audio Codec ID",
   *     in="path",
   *     required=true,
   *     example="1",
   *     @OA\Schema(type="integer", format="int64"),
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function deleteAudio($id): JsonResponse {
    $this->codecRepository->deleteAudio($id);

    return DefaultResponse::success();
  }

  /**
   * @OA\Get(
   *   tags={"Codec"},
   *   path="/api/codecs/video",
   *   summary="Get All Video Codecs",
   *   security={{"token":{}}},
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       @OA\Property(
   *         property="data",
   *         type="array",
   *         @OA\Items(ref="#/components/schemas/CodecVideo"),
   *       ),
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function getVideo(): JsonResponse {
    return response()->json([
      'data' => $this->codecRepository->getVideo(),
    ]);
  }

  /**
   * @OA\Post(
   *   tags={"Codec"},
   *   path="/api/codecs/video",
   *   summary="Add a Video Codec",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="codec",
   *     in="query",
   *     required=true,
   *     example="Sample Codec",
   *     @OA\Schema(type="string"),
   *   ),
   *   @OA\Parameter(
   *     name="order",
   *     in="query",
   *     example="1",
   *     @OA\Schema(type="integer", format="int32"),
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function addVideo(Request $request): JsonResponse {
    $this->codecRepository->addVideo($request->all());

    return DefaultResponse::success();
  }

  /**
   * @OA\Put(
   *   tags={"Codec"},
   *   path="/api/codecs/video/{video_codec_id}",
   *   summary="Edit a Video Codec",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="video_codec_id",
   *     description="Video Codec ID",
   *     in="path",
   *     required=true,
   *     example="1",
   *     @OA\Schema(type="integer", format="int64"),
   *   ),
   *   @OA\Parameter(
   *     name="codec",
   *     in="query",
   *     required=true,
   *     example="Sample Codec",
   *     @OA\Schema(type="string"),
   *   ),
   *   @OA\Parameter(
   *     name="order",
   *     in="query",
   *     example="1",
   *     @OA\Schema(type="integer", format="int32"),
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function editVideo(Request $request, $id): JsonResponse {
    $this->codecRepository->editVideo($request->except(['_method']), $id);

    return DefaultResponse::success();
  }

  /**
   * @OA\Delete(
   *   tags={"Codec"},
   *   path="/api/codecs/video/{video_codec_id}",
   *   summary="Delete an Video Codec",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="video_codec_id",
   *     description="Video Codec ID",
   *     in="path",
   *     required=true,
   *     example="1",
   *     @OA\Schema(type="integer", format="int64"),
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function deleteVideo($id): JsonResponse {
    $this->codecRepository->deleteVideo($id);

    return DefaultResponse::success();
  }
}
