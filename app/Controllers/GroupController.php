<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Repositories\Grouprepository;

class GroupController extends Controller {

  private Grouprepository $grouprepository;

  public function __construct(Grouprepository $grouprepository) {
    $this->grouprepository = $grouprepository;
  }

  /**
   * @api {get} /api/groups Retrieve all groups
   * @apiName GroupRetrieve
   * @apiGroup Groups
   *
   * @apiHeader {String} Authorization Token received from logging-in
   *
   * @apiSuccess {String[]} data List of groups
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     {
   *       "data": [
   *         "Group 1",
   *         "Group 2",
   *         "Group 3",
   *       ]
   *     }
   *
   * @apiError Unauthorized There is no login token provided, or the login token provided is invalid
   */
  public function index(): JsonResponse {
    return response()->json([
      'data' => $this->grouprepository->getAll(),
    ]);
  }

  public function add(Request $request): JsonResponse {
    return response()->json([
      'data' => $this->grouprepository->add($request->all()),
    ]);
  }

  public function delete($id): JsonResponse {
    try {
      $this->grouprepository->delete($id);

      return response()->json([
        'status' => 200,
        'message' => 'Success',
      ]);
    } catch (ModelNotFoundException) {
      return response()->json([
        'status' => 401,
        'message' => 'Group ID does not exist',
      ], 401);
    }
  }
}
