<?php

namespace App\Controllers;

use Exception;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Repositories\GroupRepository;

class GroupController extends Controller {

  private GroupRepository $groupRepository;

  public function __construct(GroupRepository $groupRepository) {
    $this->groupRepository = $groupRepository;
  }


  /**
   * @api {get} /api/groups Retrieve all groups
   * @apiName GroupRetrieve
   * @apiGroup Groups
   *
   * @apiHeader {String} Authorization Token received from logging-in
   *
   * @apiSuccess {Object[]} data List of groups
   * @apiSuccess {String} data.uuid ID of group
   * @apiSuccess {String} data.name Name of group
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     {
   *       "data": [
   *         {
   *           "id": "9ef81943-78f0-4d1c-a831-a59fb5af339c"
   *           "name": "Group 1",
   *         }, { ... }
   *       ]
   *     }
   *
   * @apiError Unauthorized There is no login token provided, or the login token provided is invalid
   */
  public function index(): JsonResponse {
    return response()->json([
      'data' => $this->groupRepository->getAll(),
    ]);
  }


  /**
   * @api {post} /api/groups/ Add Group
   * @apiName GroupAdd
   * @apiGroup Group
   *
   * @apiHeader {String} token User login token
   *
   * @apiSuccess Success Default success message
   *
   * @apiError Unauthorized There is no login token provided, or the login token provided is invalid
   * @apiError Failed Some kind of error has happened
   */
  public function add(Request $request): JsonResponse {
    try {
      $this->groupRepository->add($request->all());

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


  /**
   * @api {put} /api/groups/:uuid Edit Group
   * @apiName GroupEdit
   * @apiGroup Group
   *
   * @apiHeader {String} token User login token
   * @apiParam {uuid} id Group ID
   * @apiParam {String} name New Group name
   *
   * @apiSuccess Success Default success message
   *
   * @apiError Unauthorized There is no login token provided, or the login token provided is invalid
   * @apiError Failed Some kind of error has happened
   */
  public function edit(Request $request, $uuid): JsonResponse {
    try {
      $this->groupRepository->edit(
        $request->except(['_method']),
        $uuid
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


  /**
   * @api {delete} /api/groups/:uuid Delete Group
   * @apiName GroupDelete
   * @apiGroup Group
   *
   * @apiHeader {String} token User login token
   * @apiParam {uuid} id Group ID
   *
   * @apiSuccess Success Default success message
   *
   * @apiError Unauthorized There is no login token provided, or the login token provided is invalid
   * @apiError Invalid The provided ID is invalid, or the item does not exist
   *
   * @apiErrorExample Invalid
   *     HTTP/1.1 401 Forbidden
   *     {
   *       "status": "401",
   *       "message": "Group ID does not existt"
   *     }
   */
  public function delete($uuid): JsonResponse {
    try {
      $this->groupRepository->delete($uuid);

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

  public function import(Request $request) {
    try {
      $file = json_decode($request->file('file')->get());
      $count = $this->groupRepository->import($file);

      return response()->json([
        'status' => 200,
        'message' => 'Success',
        'data' => [
          'acceptedImports' => $count,
          'totalJsonEntries' => count($file),
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
