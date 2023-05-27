<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Repositories\GroupRepository;

use App\Resources\DefaultResponse;

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
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
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
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
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
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function add(Request $request): JsonResponse {
    $this->groupRepository->add($request->all());

    return DefaultResponse::success();
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
   *   @OA\Parameter(
   *     name="name",
   *     in="query",
   *     required=true,
   *     example="Sample Group Name",
   *     @OA\Schema(type="string"),
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function edit(Request $request, $uuid): JsonResponse {
    $this->groupRepository->edit(
      $request->except(['_method']),
      $uuid
    );

    return DefaultResponse::success();
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
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function delete($uuid): JsonResponse {
    $this->groupRepository->delete($uuid);

    return DefaultResponse::success();
  }

  /**
   * @OA\Post(
   *   tags={"Import"},
   *   path="/api/groups/import",
   *   summary="Import a JSON file to seed data for groups table",
   *   security={{"token":{}}},
   *
   *   @OA\RequestBody(
   *     required=true,
   *     @OA\MediaType(
   *       mediaType="multipart/form-data",
   *       @OA\Schema(
   *         type="object",
   *         @OA\Property(property="file", type="string", format="binary"),
   *       ),
   *     ),
   *   ),
   *
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       example={
   *         "status": 200,
   *         "message": "Success",
   *         "data": {
   *           "acceptedImports": 0,
   *           "totalJsonEntries": 0,
   *         },
   *       },
   *       @OA\Property(property="status", type="integer", format="int32"),
   *       @OA\Property(property="message", type="integer", format="int32"),
   *       @OA\Property(
   *         property="data",
   *         @OA\Property(property="acceptedImports", type="integer", format="int32"),
   *         @OA\Property(property="totalJsonEntries", type="integer", format="int32"),
   *       ),
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function import(Request $request) {
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
  }
}
