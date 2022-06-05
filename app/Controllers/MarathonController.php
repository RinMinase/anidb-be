<?php

namespace App\Controllers;

use App\Repositories\MarathonRepository;
use Illuminate\Http\JsonResponse;

class MarathonController extends Controller {

  private MarathonRepository $marathonRepository;

  public function __construct(MarathonRepository $marathonRepository) {
    $this->marathonRepository = $marathonRepository;
  }

  public function index(): JsonResponse {
    return response()->json([
      'data' => $this->marathonRepository->getAll(),
    ]);
  }
}
