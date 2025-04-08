<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;

use App\Enums\ExportTypesEnum;
use App\Exceptions\Export\FileIncompleteException;
use App\Jobs\ProcessExports;
use App\Models\Export;

class ExportRepository {

  public function get_all() {
    return Export::all();
  }

  public function get_download_url($uuid) {
    $export = Export::where('id', $uuid)->firstOrFail();

    if (!$export->is_finished) {
      throw new FileIncompleteException;
    }

    if (!Storage::disk('local')->exists('db-dumps/' . $export->id . '.' . $export->type)) {
      throw new ModelNotFoundException;
    }

    return Storage::disk('local')->temporaryUrl($export->id . '.' . $export->type, now()->addMinutes(10));
  }

  public function download($path) {
    if (!Storage::disk('local')->exists('db-dumps/' . $path)) {
      throw new ModelNotFoundException;
    }

    $has_matches = preg_match(
      '/([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})\.(\w+)/',
      $path,
      $matches
    );

    if ($has_matches && $matches[1] && $matches[2]) {
      $export = Export::where('id', $matches[1])->firstOrFail();

      if (!$export->is_finished) {
        throw new FileIncompleteException;
      }

      $filename = $export->created_at;
      $filename = str_replace(':', '-', $filename);
      $filename = str_replace(' ', '_', $filename);
      $filename .= $export->is_automated ? '_automated' : '';
      $filename .= '.' . $export->type;

      $headers = ['Content-Type' => 'application/' . $matches[2]];

      return [
        'file' => Storage::disk('local')->path('db-dumps/' . $path),
        'filename' => $filename,
        'headers' => $headers,
      ];
    }

    throw new ModelNotFoundException;
  }

  public static function generate_export(ExportTypesEnum $type, bool $is_automated) {
    Export::create([
      'type' => $type->value,
      "is_finished" => false,
      'is_automated' => $is_automated,
    ]);

    // Dispatches to background task queue
    ProcessExports::dispatch();
  }
}
