<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Repositories\BucketSimRepository;

class BucketSimController extends Controller {

  private BucketSimRepository $bucketSimRepository;

  public function __construct(BucketSimRepository $bucketSimRepository) {
    $this->bucketSimRepository = $bucketSimRepository;
  }


  /**
   * @api {get} /api/bucket-sims Retrieve All Bucket Sims
   * @apiName RetrieveAllBucketSim
   * @apiGroup BucketSim
   *
   * @apiHeader {String} token User login token
   *
   * @apiSuccess {Object[]} data Bucket Simulation list
   * @apiSuccess {UUID} data.id Entry ID
   * @apiSuccess {String} data.description Bucket Sim description
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     [
   *       {
   *         "uuid": 9ef81943-78f0-4d1c-a831-a59fb5af339c,
   *         "description": "Some bucket sim",
   *       }, { ... }
   *     ]
   *
   * @apiError Unauthorized There is no login token provided, or the login token provided is invalid
   */
  public function index(): JsonResponse {
    return response()->json([
      'data' => $this->bucketSimRepository->getAll(),
    ]);
  }


  /**
   * @api {get} /api/bucket-sims/:id Retrieve Bucket Sim
   * @apiName RetrieveBucketSim
   * @apiGroup BucketSim
   *
   * @apiHeader {String} token User login token
   * @apiParam {String} id Bucket Simulation ID
   *
   * @apiSuccess {Object[]} data Buckets with entries data
   * @apiSuccess {UUID} data.id Entry ID
   * @apiSuccess {String} data.from Filesize in nearest byte unit
   * @apiSuccess {String} data.free Filesize in nearest byte unit
   * @apiSuccess {String} data.freeTB Filesize in nearest byte unit
   * @apiSuccess {String} data.used Filesize in nearest byte unit
   * @apiSuccess {Number} data.percent Filesize in nearest byte unit
   * @apiSuccess {String} data.total Filesize in nearest byte unit
   * @apiSuccess {Number} data.rawTotal Filesize in nearest byte unit
   * @apiSuccess {Number} data.titles Filesize in nearest byte unit
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     [
   *       {
   *         "id": null,
   *         "from": null,
   *         "to": null,
   *         "free": "1.11 TB",
   *         "freeTB": "1.11 TB",
   *         "used": "123.12 GB",
   *         "percent": 10,
   *         "total": "1.23 TB",
   *         "rawTotal": 1000169533440,
   *         "titles": 1
   *       }, {
   *         "id": 1,
   *         "from": "a",
   *         "to": "d",
   *         "free": "1.11 TB",
   *         "freeTB": null,
   *         "used": "123.12 GB",
   *         "percent": 10,
   *         "total": "1.23 TB",
   *         "rawTotal": 1000169533440,
   *         "titles": 1
   *       }, { ... }
   *     ]
   *
   * @apiError Unauthorized There is no login token provided, or the login token provided is invalid
   */
  public function get($uuid): JsonResponse {
    return response()->json([
      'data' => $this->bucketSimRepository->get($uuid),
    ]);
  }

  public function add(Request $request): JsonResponse {
    try {
      $this->bucketSimRepository->add($request->all());

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

  public function edit(Request $request, $uuid): JsonResponse {
    try {
      $this->bucketSimRepository->edit($request->all(), $uuid);

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

  public function delete($uuid): JsonResponse {
    try {
      $this->bucketSimRepository->delete($uuid);

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
}
