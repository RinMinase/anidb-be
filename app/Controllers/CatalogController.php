<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Repositories\CatalogRepository;

class CatalogController extends Controller {

  private CatalogRepository $catalogRepository;

  public function __construct(CatalogRepository $catalogRepository) {
    $this->catalogRepository = $catalogRepository;
  }

  public function index(): JsonResponse {
    return response()->json([
      'data' => $this->catalogRepository->getAll(),
    ]);
  }

  public function get($id): JsonResponse {
    return response()->json([
      'data' => $this->catalogRepository->get($id),
    ]);
  }

  public function add(Request $request): JsonResponse {
    return response()->json([
      'data' => $this->entryRepository->add($request->all()),
    ]);
  }

  public function edit(Request $request, $id): JsonResponse {
    if (!isset($id)) {
      return $this->groupEdit($request);
    } else {
      return $this->singleEdit($request, $id);
    }
  }

  public function delete($id): JsonResponse {
    try {
      return response()->json([
        'data' => $this->entryRepository->delete($id),
      ]);
    } catch (ModelNotFoundException) {
      return response()->json([
        'status' => 401,
        'message' => 'Catalog ID does not exist',
      ], 401);
    }
  }

  private function singleEdit(Request $request, $id): JsonResponse {
    return response()->json([
      'data' => $this->entryRepository->edit($request->all(), $id),
    ]);
  }

  private function groupEdit(Request $request): JsonResponse {
    return response()->json([
      'data' => [],
    ]);
  }
}
