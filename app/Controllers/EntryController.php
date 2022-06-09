<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Repositories\EntryRepository;
use App\Requests\Entry\AddRequest;
use App\Requests\Entry\EditRequest;

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

  public function add(AddRequest $request): JsonResponse {
    return response()->json([
      'data' => $this->entryRepository->add($request->all()),
    ]);
  }

  public function edit(EditRequest $request, $id): JsonResponse {
    return response()->json([
      'data' => $this->entryRepository->edit($request->all(), $id),
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
