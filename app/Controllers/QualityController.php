<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\QualityRepository;
use App\Resources\Quality\QualityCollection;

class QualityController extends Controller {

  private QualityRepository $qualityRepository;

  public function __construct(QualityRepository $qualityRepository) {
    $this->qualityRepository = $qualityRepository;
  }

  public function index(): JsonResponse {
    return response()->json([
      'data' => QualityCollection::collection($this->qualityRepository->getAll()),
    ]);
  }
}
