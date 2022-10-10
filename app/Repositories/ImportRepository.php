<?php

namespace App\Repositories;

class ImportRepository {

  public function import(object $values) {
    $entry_count = 0;

    if (isset($values->entry)) {
      $entry_repo = new EntryImportRepository();
      $entry_count = $entry_repo->import($values->entry);
    }

    $bucket_count = 0;
    if (isset($values->bucket)) {
      $bucket_repo = new BucketRepository();
      $bucket_count = $bucket_repo->import($values->bucket);
    }

    $sequence_count = 0;
    if (isset($values->sequence)) {
      $sequence_repo = new SequenceRepository();
      $sequence_count = $sequence_repo->import($values->sequence);
    }

    $group_count = 0;
    if (isset($values->sequence)) {
      $group_repo = new GroupRepository();
      $group_count = $group_repo->import($values->group);
    }

    return [
      'entry' => $entry_count,
      'bucket' => $bucket_count,
      'sequence' => $sequence_count,
      'group' => $group_count,
    ];
  }
}
