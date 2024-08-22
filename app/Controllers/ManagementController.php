<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\ManagementRepository;

use App\Resources\DefaultResponse;

class ManagementController extends Controller {

  private ManagementRepository $managementRepository;

  public function __construct(ManagementRepository $managementRepository) {
    $this->managementRepository = $managementRepository;
  }

  /**
   * @OA\Get(
   *   tags={"Management"},
   *   path="/api/management",
   *   summary="Get Management Information",
   *   security={{"token":{}}},
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       allOf={
   *         @OA\Schema(ref="#/components/schemas/DefaultSuccess"),
   *         @OA\Schema(
   *           @OA\Property(
   *             property="data",
   *             ref="#/components/schemas/ManagementSchema"
   *           ),
   *         ),
   *       },
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function index(): JsonResponse {
    return DefaultResponse::success(null, [
      'data' => $this->managementRepository->index(),
    ]);
  }
}
/**
 * @OA\Schema(
 *   @OA\Property(
 *     property="count",
 *
 *     @OA\Property(
 *       property="entries",
 *       type="integer",
 *       format="int32",
 *       description="Total Entries",
 *       example=0,
 *     ),
 *     @OA\Property(
 *       property="buckets",
 *       type="integer",
 *       format="int32",
 *       description="Total Buckets",
 *       example=0,
 *     ),
 *     @OA\Property(
 *       property="partials",
 *       type="integer",
 *       format="int32",
 *       description="Total Partials",
 *       example=0,
 *     ),
 *   ),
 *
 *   @OA\Property(
 *     property="stats",
 *
 *     @OA\Property(
 *       property="watchSeconds",
 *       type="integer",
 *       format="int64",
 *       description="Watch time in seconds",
 *       example=0,
 *     ),
 *     @OA\Property(
 *       property="watch",
 *       type="string",
 *       description="Watch time in days",
 *       example="10 days"
 *     ),
 *     @OA\Property(
 *       property="watchSubtext",
 *       type="string",
 *       description="Watch time subtext",
 *       example="10 hours, 10 minutes, 10 seconds",
 *     ),
 *     @OA\Property(
 *       property="rewatchSeconds",
 *       type="integer",
 *       format="int64",
 *       description="Watch with Rewatch time in seconds",
 *       example=0,
 *     ),
 *     @OA\Property(
 *       property="rewatch",
 *       type="string",
 *       description="Watch with Rewatch time in days",
 *       example="10 days",
 *     ),
 *     @OA\Property(
 *       property="rewatchSubtext",
 *       type="string",
 *       description="Watch with Rewatch time subtext",
 *       example="10 hours, 10 minutes, 10 seconds",
 *     ),
 *
 *     @OA\Property(
 *       property="bucketSize",
 *       type="string",
 *       description="Total Buckets size",
 *       example="0 TB",
 *     ),
 *     @OA\Property(
 *       property="entrySize",
 *       type="string",
 *       description="Total Entries size",
 *       example="0 TB",
 *     ),
 *
 *     @OA\Property(
 *       property="episodes",
 *       type="integer",
 *       format="int64",
 *       description="Total episode count",
 *       example=0,
 *     ),
 *     @OA\Property(
 *       property="titles",
 *       type="integer",
 *       format="int64",
 *       description="Total title count",
 *       example=0,
 *     ),
 *     @OA\Property(
 *       property="seasons",
 *       type="integer",
 *       format="int64",
 *       description="Total season count",
 *       example=0,
 *     ),
 *   ),
 *
 *   @OA\Property(
 *     property="graph",
 *
 *     @OA\Property(
 *       property="quality",
 *       description="Titles watched per quality",
 *       @OA\Property(property="quality2160", type="integer", format="int32", example=0),
 *       @OA\Property(property="quality1080", type="integer", format="int32", example=0),
 *       @OA\Property(property="quality720", type="integer", format="int32", example=0),
 *       @OA\Property(property="quality480", type="integer", format="int32", example=0),
 *       @OA\Property(property="quality360", type="integer", format="int32", example=0),
 *     ),
 *     @OA\Property(
 *       property="months",
 *       description="Titles watched per month",
 *       @OA\Property(property="jan", type="integer", format="int32", example=0),
 *       @OA\Property(property="feb", type="integer", format="int32", example=0),
 *       @OA\Property(property="mar", type="integer", format="int32", example=0),
 *       @OA\Property(property="apr", type="integer", format="int32", example=0),
 *       @OA\Property(property="may", type="integer", format="int32", example=0),
 *       @OA\Property(property="jun", type="integer", format="int32", example=0),
 *       @OA\Property(property="jul", type="integer", format="int32", example=0),
 *       @OA\Property(property="aug", type="integer", format="int32", example=0),
 *       @OA\Property(property="sep", type="integer", format="int32", example=0),
 *       @OA\Property(property="oct", type="integer", format="int32", example=0),
 *       @OA\Property(property="nov", type="integer", format="int32", example=0),
 *       @OA\Property(property="dec", type="integer", format="int32", example=0),
 *     ),
 *   ),
 * ),
 */
class ManagementSchema {
}
