<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use App\Repositories\BucketRepository;

class BucketController extends Controller {

  private BucketRepository $bucketRepository;

  public function __construct(BucketRepository $bucketRepository) {
    $this->bucketRepository = $bucketRepository;
  }

  /**
   * @api {get} /api/buckets Retrieve all buckets
   * @apiName BucketRetrieve
   * @apiGroup Bucket
   *
   * @apiHeader {String} Authorization Token received from logging-in
   *
   * @apiSuccess {Object[]} data Bucket Data
   * @apiSuccess {Number} data.id ID of the library
   * @apiSuccess {String} data.from Starting letter of library
   * @apiSuccess {String} data.to Ending letter of library
   * @apiSuccess {Number} data.size Size of the library
   * @apiSuccess {DateTime} data.created_at Creation date of the library
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     {
   *       "data": [
   *         {
   *           id: 1,
   *           from: "a",
   *           to: "d",
   *           size: 2000339066880,
   *           created_at: "2020-01-01 00:00:00",
   *         }
   *       ]
   *     }
   *
   * @apiError Unauthorized There is no login token provided, or the login token provided is invalid
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
    return response()->json([
      'data' => $this->bucketRepository->add($request->all()),
    ]);
  }

  public function edit(Request $request, $id): JsonResponse {
    return response()->json([
      'data' => $this->bucketRepository->edit($request->all(), $id),
    ]);
  }

  public function delete($id): JsonResponse {
    try {
      return response()->json([
        'data' => $this->bucketRepository->delete($id),
      ]);
    } catch (ModelNotFoundException) {
      return response()->json([
        'status' => 401,
        'message' => 'Catalog ID does not exist',
      ], 401);
    }
  }
}
