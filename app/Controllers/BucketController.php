<?php

namespace App\Controllers;

use Exception;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Repositories\BucketRepository;

class BucketController extends Controller {

  private BucketRepository $bucketRepository;

  public function __construct(BucketRepository $bucketRepository) {
    $this->bucketRepository = $bucketRepository;
  }

  /**
   * @OA\Get(
   *   tags={"Bucket"},
   *   path="/api/buckets",
   *   summary="Get All Buckets",
   *   security={{"token":{}}},
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       @OA\Property(
   *         property="data",
   *         type="array",
   *         @OA\Items(ref="#/components/schemas/Bucket"),
   *       ),
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   * )
   */
  public function index(): JsonResponse {
    return response()->json([
      'data' => $this->bucketRepository->getAll(),
    ]);
  }

  public function get($id): JsonResponse {
    return response()->json([
      'data' => $this->bucketRepository->get($id),
    ]);
  }

  public function add(Request $request): JsonResponse {
    try {
      $this->bucketRepository->add($request->all());

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

  public function edit(Request $request, $id): JsonResponse {
    try {
      $this->bucketRepository->edit(
        $request->except(['_method']),
        $id
      );

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

  public function delete($id): JsonResponse {
    try {
      $this->bucketRepository->delete($id);

      return response()->json([
        'status' => 200,
        'message' => 'Success',
      ]);
    } catch (ModelNotFoundException) {
      return response()->json([
        'status' => 401,
        'message' => 'Bucket ID does not exist',
      ], 401);
    }
  }

  public function import(Request $request) {
    try {
      $count = $this->bucketRepository->import($request->all());

      return response()->json([
        'status' => 200,
        'message' => 'Success',
        'data' => [
          'acceptedImports' => $count,
          'totalJsonEntries' => count($request->all()),
        ],
      ]);
    } catch (Exception $e) {
      throw $e;
      return response()->json([
        'status' => 401,
        'message' => 'Failed to import JSON file',
      ]);
    }
  }
}
