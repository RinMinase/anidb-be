<?php

namespace App\Controllers;

use Exception;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Repositories\ImportRepository;

class ImportController extends Controller {

  private ImportRepository $importRepository;

  public function __construct(ImportRepository $importRepository) {
    $this->importRepository = $importRepository;
  }

  /**
   * @OA\Post(
   *   tags={"Import"},
   *   path="/api/import",
   *   summary="Import a JSON file to seed data for all tables",
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
   *           "entries": {"accepted": 0, "total": 0},
   *           "buckets": {"accepted": 0, "total": 0},
   *           "sequences": {"accepted": 0, "total": 0},
   *           "groups": {"accepted": 0, "total": 0},
   *         },
   *       },
   *       @OA\Property(property="status", type="integer", format="int32"),
   *       @OA\Property(property="message", type="integer", format="int32"),
   *       @OA\Property(
   *         property="data",
   *         @OA\Property(
   *           property="entries",
   *           @OA\Property(property="accepted", type="integer", format="int32"),
   *           @OA\Property(property="total", type="integer", format="int32"),
   *         ),
   *         @OA\Property(
   *           property="buckets",
   *           @OA\Property(property="accepted", type="integer", format="int32"),
   *           @OA\Property(property="total", type="integer", format="int32"),
   *         ),
   *         @OA\Property(
   *           property="sequences",
   *           @OA\Property(property="accepted", type="integer", format="int32"),
   *           @OA\Property(property="total", type="integer", format="int32"),
   *         ),
   *         @OA\Property(
   *           property="groups",
   *           @OA\Property(property="accepted", type="integer", format="int32"),
   *           @OA\Property(property="total", type="integer", format="int32"),
   *         ),
   *       ),
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function index(Request $request): JsonResponse {
    $file = json_decode($request->file('file')->get());
    $import_count = $this->importRepository->import($file);

    $entry_count = 0;
    if (!empty($file->entry)) {
      $entry_count = count($file->entry);
    }

    $bucket_count = 0;
    if (!empty($file->bucket)) {
      $bucket_count = count($file->bucket);
    }

    $sequence_count = 0;
    if (!empty($file->sequence)) {
      $sequence_count = count($file->sequence);
    }

    $group_count = 0;
    if (!empty($file->group)) {
      $group_count = count($file->group);
    }

    return response()->json([
      'status' => 200,
      'message' => 'Success',
      'data' => [
        'entries' => [
          'accepted' => $import_count['entry'],
          'total' => $entry_count,
        ],
        'buckets' => [
          'accepted' => $import_count['bucket'],
          'total' => $bucket_count,
        ],
        'sequences' => [
          'accepted' => $import_count['sequence'],
          'total' => $sequence_count,
        ],
        'groups' => [
          'accepted' => $import_count['group'],
          'total' => $group_count,
        ],
      ]
    ]);
  }
}
