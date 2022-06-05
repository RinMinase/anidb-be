<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Repositories\LogRepository;

class LogController extends Controller {

  private LogRepository $logRepository;

  public function __construct(LogRepository $logRepository) {
    $this->logRepository = $logRepository;
  }

  public function index(): JsonResponse {
    return response()->json([
      'data' => $this->logRepository->getAll(),
    ]);
  }
}
