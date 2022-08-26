<?php

namespace App\Repositories;

use App\Models\BucketSim;
use App\Models\BucketSimInfo;

class BucketSimRepository {

  public function getAll() {
    return BucketSimInfo::all();
  }

  public function get($uuid) {
    $info = BucketSimInfo::where('uuid', $uuid)->firstOrFail();

    $buckets = BucketSim::select('from', 'to', 'size')
      ->where('id_sim_info', $info->id)
      ->orderBy('from')
      ->get();

    $entryRepo = new EntryRepository();

    return $entryRepo->getBuckets($buckets);
  }
}
