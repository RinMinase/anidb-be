<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Repositories\CodecRepository;

class CodecController extends Controller {

  private CodecRepository $codecRepository;

  public function __construct(CodecRepository $codecRepository) {
    $this->codecRepository = $codecRepository;
  }

  /**
   * @OA\Get(
   *   tags={"Dropdowns"},
   *   path="/api/codecs",
   *   summary="Get All Codecs",
   *   security={{"token":{}}},
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       @OA\Property(
   *         property="data",
   *         type="object",
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
   *   @OA\Response(
   *     response=401,
   *     description="Unauthorized",
   *     @OA\JsonContent(ref="#/components/schemas/Unauthorized"),
   *   ),
   * )
   */
  public function index(): JsonResponse {
    return response()->json([
      'data' => $this->codecRepository->getAll(),
    ]);
  }

  /**
   * @OA\Get(
   *   tags={"Dropdowns"},
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
   *   @OA\Response(
   *     response=401,
   *     description="Unauthorized",
   *     @OA\JsonContent(ref="#/components/schemas/Unauthorized"),
   *   ),
   * )
   */
  public function getAudio(): JsonResponse {
    return response()->json([
      'data' => $this->codecRepository->getAudio(),
    ]);
  }

  public function addAudio(Request $request): JsonResponse {
    try {
      $this->codecRepository->addAudio($request->all());

      return response()->json([
        'status' => 200,
        'message' => 'Success',
      ]);
    } catch (Exception) {
      return response()->json([
        'status' => 500,
        'message' => 'Failed',
      ], 500);
    }
  }

  public function editAudio(Request $request, $id): JsonResponse {
    try {
      $this->codecRepository->addAudio($request->except(['_method']), $id);

      return response()->json([
        'status' => 200,
        'message' => 'Success',
      ]);
    } catch (Exception) {
      return response()->json([
        'status' => 500,
        'message' => 'Failed',
      ], 500);
    }
  }

  public function deleteAudio($id): JsonResponse {
    try {
      $this->codecRepository->deleteAudio($id);

      return response()->json([
        'status' => 200,
        'message' => 'Success',
      ]);
    } catch (ModelNotFoundException) {
      return response()->json([
        'status' => 401,
        'message' => 'Codec does not exist',
      ], 401);
    }
  }

  /**
   * @OA\Get(
   *   tags={"Dropdowns"},
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
   *   @OA\Response(
   *     response=401,
   *     description="Unauthorized",
   *     @OA\JsonContent(ref="#/components/schemas/Unauthorized"),
   *   ),
   * )
   */
  public function getVideo(): JsonResponse {
    return response()->json([
      'data' => $this->codecRepository->getVideo(),
    ]);
  }

  public function addVideo(Request $request): JsonResponse {
    try {
      $this->codecRepository->addVideo($request->all());

      return response()->json([
        'status' => 200,
        'message' => 'Success',
      ]);
    } catch (Exception) {
      return response()->json([
        'status' => 500,
        'message' => 'Failed',
      ], 500);
    }
  }

  public function editVideo(Request $request, $id): JsonResponse {
    try {
      $this->codecRepository->editVideo($request->except(['_method']), $id);

      return response()->json([
        'status' => 200,
        'message' => 'Success',
      ]);
    } catch (Exception) {
      return response()->json([
        'status' => 500,
        'message' => 'Failed',
      ], 500);
    }
  }

  public function deleteVideo($id): JsonResponse {
    try {
      $this->codecRepository->deleteVideo($id);

      return response()->json([
        'status' => 200,
        'message' => 'Success',
      ]);
    } catch (ModelNotFoundException) {
      return response()->json([
        'status' => 401,
        'message' => 'Codec does not exist',
      ], 401);
    }
  }
}
