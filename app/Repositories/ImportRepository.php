<?php

namespace App\Repositories;

class ImportRepository {

  public function import(array $values) {
    $entry_count = 0;
    if (isset($values['entries'])) {
      $entry_repo = new EntryImportRepository();
      $entry_count = $entry_repo->import($values['entries']);
    }

    $bucket_count = 0;
    if (isset($values['buckets'])) {
      $bucket_repo = new BucketRepository();
      $bucket_count = $bucket_repo->import($values['buckets']);
    }

    $sequence_count = 0;
    if (isset($values['sequences'])) {
      $sequence_repo = new SequenceRepository();
      $sequence_count = $sequence_repo->import($values['sequences']);
    }

    return [
      'entry' => $entry_count,
      'bucket' => $bucket_count,
      'sequence' => $sequence_count,
    ];
  }
}
