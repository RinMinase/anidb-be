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
      $import_count = $this->importRepository->import($request->all());

      $entry_count = 0;
      if (!empty($request->all()['entry'])) {
        $entry_count = count($request->all()['entry']);
      }

      $bucket_count = 0;
      if (!empty($request->all()['bucket'])) {
        $bucket_count = count($request->all()['bucket']);
      }

      $sequence_count = 0;
      if (!empty($request->all()['sequence'])) {
        $sequence_count = count($request->all()['sequence']);
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
