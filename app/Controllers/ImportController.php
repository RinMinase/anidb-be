<?php

namespace App\Controllers;

use Exception;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\Entry;

class ImportController extends Controller {

  public function index(Request $request) {
    try {
      $import = [];

      foreach ($request->all() as $item) {
        array_push($import, [
          'uuid' => Str::uuid()->toString(),
          'title' => $item['title'] ?? null,
          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
      }

      Entry::insert($import);

      return response()->json([
        'status' => 200,
        'message' => 'Success',
      ]);
    } catch (Exception) {
      return response()->json([
        'status' => 401,
        'message' => 'Failed to import JSON file',
      ]);
    }
  }
}
