<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\PriorityRepository;

class PriorityController extends Controller {

  private PriorityRepository $priorityRepository;

  public function __construct(PriorityRepository $priorityRepository) {
    $this->priorityRepository = $priorityRepository;
  }


  /**
   * @api {get} /api/qualities Retrieve all priorities
   * @apiName PriorityRetrieve
   * @apiGroup Priorities
   *
   * @apiHeader {String} Authorization Token received from logging-in
   *
   * @apiSuccess {Object[]} data Priority Array
   * @apiSuccess {Number} data.id Priority ID
   * @apiSuccess {String} data.priority Priority description
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     {
   *       "data": [
   *         {
   *           "id": 1,
   *           "priority": "High"
   *         }, { ... }
   *       ]
   *     }
   *
   * @apiError Unauthorized There is no login token provided, or the login token provided is invalid
   */
  public function index(): JsonResponse {
    return response()->json([
      'data' => $this->priorityRepository->getAll(),
    ]);
  }
}
