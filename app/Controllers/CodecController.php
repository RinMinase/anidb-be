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
   * @api {get} /api/codecs Retrieve all codecs
   * @apiName CodecRetrieve
   * @apiGroup Codecs
   *
   * @apiHeader {String} Authorization Token received from logging-in
   *
   * @apiSuccess {Object[]} data.audio Audio Codec Array
   * @apiSuccess {Number} data.audio.id Audio Codec ID
   * @apiSuccess {String} data.audio.codec Audio Codec description
   * @apiSuccess {Object[]} data.video Video Codec Array
   * @apiSuccess {Number} data.video.id Video Codec ID
   * @apiSuccess {String} data.video.codec Video Codec description
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     {
   *       "audio": [
   *         {
   *           "id": 1,
   *           "codec": "AAC 2.0"
   *         }, { ... }
   *       ]
   *       "video": [
   *         {
   *           "id": 1,
   *           "codec": "x264 8bit"
   *         }, { ... }
   *       ]
   *     }
   *
   * @apiError Unauthorized There is no login token provided, or the login token provided is invalid
   */
  public function index(): JsonResponse {
    return response()->json([
      'data' => $this->codecRepository->getAll(),
    ]);
  }

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
