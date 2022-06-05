<?php

namespace App\Controllers;

use App\Repositories\HddRepository;
use Illuminate\Http\JsonResponse;

class HddController extends Controller {

  private HddRepository $hddRepository;

  public function __construct(HddRepository $hddRepository)
  {
      $this->hddRepository = $hddRepository;
  }

  public function index(): JsonResponse {
    return response()->json([
      'data' => $this->hddRepository->getAll(),
    ]);
  }

}
