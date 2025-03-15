<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;

use App\Exceptions\Export\FileIncompleteException;
use App\Models\Export;

class ExportRepository {

  public function get_all() {
    return Export::all();
  }

  public function get_download_url($uuid) {
    $export = Export::where('id', $uuid)->get()->firstOrFail();

    if (!$export->is_finished) {
      throw new FileIncompleteException;
    }

    $file = storage_path('app/db-dumps/') . $export->id . '.' . $export->type;

    if (file_exists($file)) {
      return Storage::temporaryUrl($file, now()->addMinutes(10));
    }

    throw new ModelNotFoundException();
  }

  public function download($path) {
    $has_matches = preg_match(
      '/([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})\.(\w+)/',
      $path,
      $matches
    );

    if ($has_matches && $matches[1] && $matches[2]) {
      $export = Export::where('id', $matches[1])->get()->firstOrFail();

      $filename = $export->created_at;
      $filename = str_replace(':', '-', $filename);
      $filename = str_replace(' ', '_', $filename);
      $filename .= $export->is_automated ? '_automated' : '';
      $filename .= $export->type;

      $headers = ['Content-Type' => 'application/' . $matches[2]];

      return [
        'file' => $path,
        'filename' => $filename,
        'headers' => $headers,
      ];
    }

    throw new ModelNotFoundException;
  }
}
