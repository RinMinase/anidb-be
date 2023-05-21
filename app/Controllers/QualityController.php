<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\QualityRepository;

class QualityController extends Controller {

  private QualityRepository $qualityRepository;

  public function __construct(QualityRepository $qualityRepository) {
    $this->qualityRepository = $qualityRepository;
  }

  /**
   * @OA\Get(
   *   tags={"Dropdowns"},
   *   path="/api/qualities",
   *   summary="Get All Qualities",
   *   security={{"token":{}}},
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       @OA\Property(
   *         property="data",
   *         type="array",
   *         @OA\Items(ref="#/components/schemas/Quality"),
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
      'data' => $this->qualityRepository->getAll(),
    ]);
  }
}
