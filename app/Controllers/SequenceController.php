<?php

namespace App\Controllers;

use Exception;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Repositories\SequenceRepository;
use App\Resources\DefaultResponse;
use App\Resources\ErrorResponse;

class SequenceController extends Controller {

  private SequenceRepository $sequenceRepository;

  public function __construct(SequenceRepository $sequenceRepository) {
    $this->sequenceRepository = $sequenceRepository;
  }

  /**
   * @OA\Get(
   *   tags={"Sequence"},
   *   path="/api/sequences",
   *   summary="Get All Sequences",
   *   security={{"token":{}}},
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       @OA\Property(
   *         property="data",
   *         type="array",
   *         @OA\Items(ref="#/components/schemas/Sequence"),
   *       ),
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function index(): JsonResponse {
    return response()->json([
      'data' => $this->sequenceRepository->getAll(),
    ]);
  }

  /**
   * @OA\Get(
   *   tags={"Sequence"},
   *   path="/api/sequences/{sequence_id}",
   *   summary="Get Sequence",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="sequence_id",
   *     description="Sequence ID",
   *     in="path",
   *     required=true,
   *     example=1,
   *     @OA\Schema(type="integer", format="int32"),
   *   ),
   *
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(ref="#/components/schemas/Sequence"),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function get($id): JsonResponse {
    return response()->json($this->sequenceRepository->get($id));
  }

  /**
   * @OA\Post(
   *   tags={"Sequence"},
   *   path="/api/sequences",
   *   summary="Add a Sequence",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="title",
   *     in="query",
   *     required=true,
   *     example="Sample Sequence List",
   *     @OA\Schema(type="string"),
   *   ),
   *   @OA\Parameter(
   *     name="date_from",
   *     in="query",
   *     required=true,
   *     example="2020-01-01",
   *     @OA\Schema(type="string", format="date"),
   *   ),
   *   @OA\Parameter(
   *     name="date_to",
   *     in="query",
   *     required=true,
   *     example="2020-02-01",
   *     @OA\Schema(type="string", format="date"),
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function add(Request $request): JsonResponse {
    $this->sequenceRepository->add($request->all());

    return DefaultResponse::success();
  }

  /**
   * @OA\Put(
   *   tags={"Sequence"},
   *   path="/api/sequences/{sequence_id}",
   *   summary="Edit a Sequence",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="sequence_id",
   *     description="Sequence ID",
   *     in="path",
   *     required=true,
   *     example=1,
   *     @OA\Schema(type="integer", format="int32"),
   *   ),
   *   @OA\Parameter(
   *     name="title",
   *     in="query",
   *     required=true,
   *     example="Sample Sequence List",
   *     @OA\Schema(type="string"),
   *   ),
   *   @OA\Parameter(
   *     name="date_from",
   *     in="query",
   *     required=true,
   *     example="2020-01-01",
   *     @OA\Schema(type="string", format="date"),
   *   ),
   *   @OA\Parameter(
   *     name="date_to",
   *     in="query",
   *     required=true,
   *     example="2020-02-01",
   *     @OA\Schema(type="string", format="date"),
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function edit(Request $request, $id): JsonResponse {
    $this->sequenceRepository->edit(
      $request->except(['_method']),
      $id
    );

    return DefaultResponse::success();
  }

  /**
   * @OA\Delete(
   *   tags={"Sequence"},
   *   path="/api/sequences/{sequence_id}",
   *   summary="Delete a Sequence",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="sequence_id",
   *     description="Sequence ID",
   *     in="path",
   *     required=true,
   *     example=1,
   *     @OA\Schema(type="integer", format="int32"),
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function delete($id): JsonResponse {
    $this->sequenceRepository->delete($id);

    return DefaultResponse::success();
  }

  /**
   * @OA\Post(
   *   tags={"Import"},
   *   path="/api/sequences/import",
   *   summary="Import a JSON file to seed data for sequences table",
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
    try {
      $count = $this->sequenceRepository->import($request->all());

      return response()->json([
        'status' => 200,
        'message' => 'Success',
        'data' => [
          'acceptedImports' => $count,
          'totalJsonEntries' => count($request->all()),
        ],
      ]);
    } catch (Exception) {
      return response()->json([
        'status' => 401,
        'message' => 'Failed to import JSON file',
      ]);
    }
  }
}
