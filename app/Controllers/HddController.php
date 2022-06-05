<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use App\Repositories\HddRepository;

class HddController extends Controller {

  private HddRepository $hddRepository;

  public function __construct(HddRepository $hddRepository) {
    $this->hddRepository = $hddRepository;
  }

  /**
   * @api {get} /api/hdd Retrieve all HDDs
   * @apiName HDDRetrieve
   * @apiGroup HDD
   *
   * @apiHeader {String} token User login token
   *
   * @apiSuccess {Object[]} data HDD Data
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
   *
   * @apiErrorExample Unauthorized
   *     HTTP/1.1 401 Forbidden
   *     {
   *       "status": "Unauthorized",
   *       "message": "Unauthorized"
   *     }
   */
  public function index(): JsonResponse {
    return response()->json([
      'data' => $this->hddRepository->getAll(),
    ]);
  }
}
