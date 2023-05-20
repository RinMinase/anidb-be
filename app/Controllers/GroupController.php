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
   * @OA\Get(
   *   tags={"Group"},
   *   path="/api/groups",
   *   summary="Get All Groups",
   *   security={{"token":{}}},
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       @OA\Property(
   *         property="data",
   *         type="array",
   *         @OA\Items(ref="#/components/schemas/Group"),
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
      'data' => $this->groupRepository->getAll(),
    ]);
  }

  /**
   * @OA\Get(
   *   tags={"Group"},
   *   path="/api/groups/names",
   *   summary="Get All Group Names",
   *   security={{"token":{}}},
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       @OA\Property(property="data", type="array", @OA\Items(type="string")),
   *     ),
   *   ),
   *   @OA\Response(
   *     response=401,
   *     description="Unauthorized",
   *     @OA\JsonContent(ref="#/components/schemas/Unauthorized"),
   *   ),
   * )
   */
  public function getNames(): JsonResponse {
    return response()->json([
      'data' => $this->groupRepository->getNames(),
    ]);
  }

  /**
   * @OA\Post(
   *   tags={"Group"},
   *   path="/api/groups",
   *   summary="Add a Group",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="name",
   *     in="query",
   *     required=true,
   *     example="Sample Group Name",
   *     @OA\Schema(type="string"),
   *   ),
   *
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       @OA\Property(
   *         property="data",
   *         type="array",
   *         @OA\Items(ref="#/components/schemas/Group"),
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
   * @OA\Put(
   *   tags={"Group"},
   *   path="/api/groups/{group_id}",
   *   summary="Edit a Group",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="group_id",
   *     description="Group ID",
   *     in="path",
   *     required=true,
   *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *     @OA\Schema(type="string", format="uuid"),
   *   ),
   *
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(ref="#/components/schemas/Success"),
   *   ),
   *   @OA\Response(
   *     response=401,
   *     description="Unauthorized",
   *     @OA\JsonContent(ref="#/components/schemas/Unauthorized"),
   *   ),
   * )
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
   * @OA\Delete(
   *   tags={"Group"},
   *   path="/api/groups/{group_id}",
   *   summary="Delete a Group",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="group_id",
   *     description="Group ID",
   *     in="path",
   *     required=true,
   *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *     @OA\Schema(type="string", format="uuid"),
   *   ),
   *
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(ref="#/components/schemas/Success"),
   *   ),
   *   @OA\Response(
   *     response=401,
   *     description="Unauthorized",
   *     @OA\JsonContent(ref="#/components/schemas/Unauthorized"),
   *   ),
   * )
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
