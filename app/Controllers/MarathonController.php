<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\MarathonRepository;

class MarathonController extends Controller {

  private MarathonRepository $marathonRepository;

  public function __construct(MarathonRepository $marathonRepository) {
    $this->marathonRepository = $marathonRepository;
  }

  /**
   * @api {get} /api/marathon Retrieve all Marathons
   * @apiName MarathonRetrieve
   * @apiGroup Marathon
   *
   * @apiHeader {String} token User login token
   *
   * @apiSuccess {Object[]} data Marathon Data
   * @apiSuccess {Number} data.id ID of the marathon entry
   * @apiSuccess {String} data.title Descriptive title of the marathon
   * @apiSuccess {String} data.date_from Start date of the marathon
   * @apiSuccess {Number} data.date_to End date of the marathon
   * @apiSuccess {DateTime} data.created_at Creation date of the marathon entry
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     {
   *       "data": [
   *         {
   *           id: 1
   *           title: "Summer List",
   *           date_from: "2020-01-01",
   *           date_to: "2020-02-01",
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
      'data' => $this->marathonRepository->getAll(),
    ]);
  }
}
