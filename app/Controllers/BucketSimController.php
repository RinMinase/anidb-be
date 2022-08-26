<?php

namespace App\Controllers;


use Illuminate\Http\JsonResponse;

use App\Repositories\bucketSimRepository;

class BucketSimController extends Controller {

  private bucketSimRepository $bucketSimRepository;

  public function __construct(bucketSimRepository $bucketSimRepository) {
    $this->bucketSimRepository = $bucketSimRepository;
  }

  public function index(): JsonResponse {
    return response()->json([
      'data' => $this->bucketSimRepository->getAll(),
    ]);
  }

  public function get($uuid): JsonResponse {
    return response()->json([
      'data' => $this->bucketSimRepository->get($uuid),
    ]);
  }
}
