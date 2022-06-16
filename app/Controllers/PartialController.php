<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Requests\Partial\AddRequest;
use App\Requests\Partial\EditRequest;

class PartialController extends Controller {

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
        'message' => 'Partial ID does not exist',
      ], 401);
    }
  }
}
