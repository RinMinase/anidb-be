<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;
use PhpZip\Exception\ZipException;
use PhpZip\ZipFile;

use App\Enums\ExportTypesEnum;
use App\Exceptions\Export\FileIncompleteException;
use App\Exceptions\Export\ZipFileProcessException;
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

    return Storage::disk('local')->temporaryUrl($export->id, now()->addMinutes(10));
  }

  public function download(string $uuid) {
    $export = Export::where('id', $uuid)->firstOrFail();
    $filename = $export->id . '.' . $export->type;

    if (!$export->is_finished) {
      throw new FileIncompleteException;
    }

    if (!Storage::disk('local')->exists('db-dumps/' . $filename)) {
      throw new ZipFileProcessException;
    }

    $zip = new ZipFile();

    try {
      $zip->addFile(Storage::disk('local')->path('db-dumps/' . $filename))
        ->saveAsFile(Storage::disk('local')->path('db-dumps/temp.zip'))
        ->close();
    } catch (ZipException) {
      throw new ZipFileProcessException;
    } finally {
      $zip->close();
    }

    // Filename format <yyyy-mm-dd_hh-mm-ss><_automated>.zip
    $zip_filename = $export->created_at;
    $zip_filename = str_replace(':', '-', $zip_filename);
    $zip_filename = str_replace(' ', '_', $zip_filename);
    $zip_filename .= $export->is_automated ? '_automated' : '';
    $zip_filename .= '.zip';

    return [
      'file' => Storage::disk('local')->path('db-dumps/temp.zip'),
      'filename' => $zip_filename,
      'headers' => ['Content-Type' => 'application/zip'],
    ];
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
