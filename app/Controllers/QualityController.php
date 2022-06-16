<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\QualityRepository;
use App\Resources\Quality\QualityCollection;

class QualityController extends Controller {

  /**
   * @OA\Info(
   *      version="1.0.0",
   *      title="Quality",
   *      description="Description",
   * )
   *
   * @OA\Server(
   *      url=L5_SWAGGER_CONST_HOST,
   * )
   */

  private QualityRepository $qualityRepository;

  public function __construct(QualityRepository $qualityRepository) {
    $this->qualityRepository = $qualityRepository;
  }

  /**
   * @OA\Get(
   *   path="/quality",
   *   description="Retrieve Quality List",
   *   @OA\Response(
   *    response=200,
   *    description="Success"
   *   ),
   *   @OA\Response(
   *     response=400,
   *     description="Bad Request"
   *   ),
   *   @OA\Response(
   *     response=401,
   *     description="Unauthenticated",
   *   ),
   *   @OA\Response(
   *     response=403,
   *     description="Forbidden"
   *   )
   * )
   */
  public function index(): JsonResponse {
    return response()->json([
      'data' => QualityCollection::collection($this->qualityRepository->getAll()),
    ]);
  }
}
