<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\EntryRepository;

class EntryBySequenceController extends Controller {

  private EntryRepository $entryRepository;

  public function __construct(EntryRepository $entryRepository) {
    $this->entryRepository = $entryRepository;
  }

  public function index($id): JsonResponse {
    return response()->json([
      'data' => $this->entryRepository->getBySequence($id),
    ]);
  }
}
