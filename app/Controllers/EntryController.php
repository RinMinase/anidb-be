<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repositories\EntryRepository;
use Exception;

class EntryController extends Controller {

  private EntryRepository $entryRepository;

  public function __construct(EntryRepository $entryRepository) {
    $this->entryRepository = $entryRepository;
  }

  public function index(): JsonResponse {
    return response()->json([
      'data' => $this->entryRepository->getAll(),
    ]);
  }

  public function get($id): JsonResponse {
    return response()->json([
      'data' => $this->entryRepository->get($id),
    ]);
  }

  public function add(Request $request): JsonResponse {
    return response()->json([
      'data' => [],
    ]);
  }

  public function edit(Request $request, $id): JsonResponse {
    return response()->json([
      'data' => [],
    ]);
  }

  public function delete($id): JsonResponse {
    try {
      return response()->json([
        'data' => $this->entryRepository->delete($id),
      ]);
    } catch (ModelNotFoundException) {
      return response()->json([
        'status' => 401,
        'message' => 'Entry ID does not exist',
      ], 401);
    }
  }
}
