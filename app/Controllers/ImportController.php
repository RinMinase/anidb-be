<?php

namespace App\Controllers;

use Exception;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Repositories\ImportRepository;

class ImportController extends Controller {

  private ImportRepository $importRepository;

  public function __construct(ImportRepository $importRepository) {
    $this->importRepository = $importRepository;
  }

  public function index(Request $request): JsonResponse {
    try {
      $file = json_decode($request->file('file')->get());
      $import_count = $this->importRepository->import($file);

      $entry_count = 0;
      if (!empty($file->entry)) {
        $entry_count = count($file->entry);
      }

      $bucket_count = 0;
      if (!empty($file->bucket)) {
        $bucket_count = count($file->bucket);
      }

      $sequence_count = 0;
      if (!empty($file->sequence)) {
        $sequence_count = count($file->sequence);
      }

      $group_count = 0;
      if (!empty($file->group)) {
        $group_count = count($file->group);
      }

      return response()->json([
        'status' => 200,
        'message' => 'Success',
        'data' => [
          'entry' => [
            'accepted' => $import_count['entry'],
            'total' => $entry_count,
          ],
          'buckets' => [
            'accepted' => $import_count['bucket'],
            'total' => $bucket_count,
          ],
          'sequences' => [
            'accepted' => $import_count['sequence'],
            'total' => $sequence_count,
          ],
          'groups' => [
            'accepted' => $import_count['group'],
            'total' => $group_count,
          ],
        ]
      ]);
    } catch (Exception) {
      return response()->json([
        'status' => 500,
        'message' => 'Failed',
      ], 500);
    }
  }
}
