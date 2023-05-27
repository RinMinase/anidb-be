<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\ManagementRepository;

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
   *       example={
   *         "data": {
   *           "entries": 0,
   *           "buckets": 0,
   *           "partials": 0,
   *         },
   *         "stats": {
   *           "watchSeconds": 0,
   *           "watch": "10 days",
   *           "watchSubtext": "10 hours, 10 minutes, 10 seconds",
   *           "rewatchSeconds": 0,
   *           "rewatch": "10 days",
   *           "rewatchSubtext": "10 hours, 10 minutes, 10 seconds",
   *           "bucketSize": "0 TB",
   *           "entrySize": "0 TB",
   *           "episodes": 0,
   *           "titles": 0,
   *           "seasons": 0,
   *         },
   *         "graph": {
   *           "quality": {
   *             "quality_2160": 0,
   *             "quality_1080": 0,
   *             "quality_720": 0,
   *             "quality_480": 0,
   *             "quality_360": 0,
   *           },
   *           "months": {
   *             "jan": 0,
   *             "feb": 0,
   *             "mar": 0,
   *             "apr": 0,
   *             "may": 0,
   *             "jun": 0,
   *             "jul": 0,
   *             "aug": 0,
   *             "sep": 0,
   *             "oct": 0,
   *             "nov": 0,
   *             "dec": 0,
   *           },
   *         },
   *       },
   *
   *       @OA\Property(
   *         property="data",
   *         type="object",
   *         @OA\Property(
   *           property="entries",
   *           type="integer",
   *           format="int32",
   *           description="Total Entries",
   *         ),
   *         @OA\Property(
   *           property="buckets",
   *           type="integer",
   *           format="int32",
   *           description="Total Buckets",
   *         ),
   *         @OA\Property(
   *           property="partials",
   *           type="integer",
   *           format="int32",
   *           description="Total Partials",
   *         ),
   *       ),
   *
   *       @OA\Property(
   *         property="stats",
   *         type="object",
   *         @OA\Property(
   *           property="watchSeconds",
   *           type="integer",
   *           format="int64",
   *           description="Watch time in seconds",
   *         ),
   *         @OA\Property(
   *           property="watch",
   *           type="string",
   *           description="Watch time in days",
   *         ),
   *         @OA\Property(
   *           property="watchSubtext",
   *           type="string",
   *           description="Watch time subtext",
   *         ),
   *         @OA\Property(
   *           property="rewatchSeconds",
   *           type="integer",
   *           format="int64",
   *           description="Watch with Rewatch time in seconds",
   *         ),
   *         @OA\Property(
   *           property="rewatch",
   *           type="string",
   *           description="Watch with Rewatch time in days",
   *         ),
   *         @OA\Property(
   *           property="rewatchSubtext",
   *           type="string",
   *           description="Watch with Rewatch time subtext",
   *         ),
   *
   *         @OA\Property(
   *           property="bucketSize",
   *           type="string",
   *           description="Total Buckets size",
   *         ),
   *         @OA\Property(
   *           property="entrySize",
   *           type="string",
   *           description="Total Entries size",
   *         ),
   *
   *         @OA\Property(
   *           property="episodes",
   *           type="integer",
   *           format="int64",
   *           description="Total episode count",
   *         ),
   *         @OA\Property(
   *           property="titles",
   *           type="integer",
   *           format="int64",
   *           description="Total title count",
   *         ),
   *         @OA\Property(
   *           property="seasons",
   *           type="integer",
   *           format="int64",
   *           description="Total season count",
   *         ),
   *       ),
   *
   *       @OA\Property(
   *         property="graph",
   *         type="object",
   *
   *         @OA\Property(
   *           property="quality",
   *           type="object",
   *           description="Titles watched per quality",
   *           @OA\Property(property="quality_2160", type="integer", format="int32"),
   *           @OA\Property(property="quality_1080", type="integer", format="int32"),
   *           @OA\Property(property="quality_720", type="integer", format="int32"),
   *           @OA\Property(property="quality_480", type="integer", format="int32"),
   *           @OA\Property(property="quality_360", type="integer", format="int32"),
   *         ),
   *         @OA\Property(
   *           property="months",
   *           type="object",
   *           description="Titles watched per month",
   *           @OA\Property(property="jan", type="integer", format="int32"),
   *           @OA\Property(property="feb", type="integer", format="int32"),
   *           @OA\Property(property="mar", type="integer", format="int32"),
   *           @OA\Property(property="apr", type="integer", format="int32"),
   *           @OA\Property(property="may", type="integer", format="int32"),
   *           @OA\Property(property="jun", type="integer", format="int32"),
   *           @OA\Property(property="jul", type="integer", format="int32"),
   *           @OA\Property(property="aug", type="integer", format="int32"),
   *           @OA\Property(property="sep", type="integer", format="int32"),
   *           @OA\Property(property="oct", type="integer", format="int32"),
   *           @OA\Property(property="nov", type="integer", format="int32"),
   *           @OA\Property(property="dec", type="integer", format="int32"),
   *         ),
   *       ),
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function index(): JsonResponse {
    return response()->json([
      'data' => $this->managementRepository->index(),
    ]);
  }
}
