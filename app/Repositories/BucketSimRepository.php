<?php

namespace App\Repositories;

use Illuminate\Support\Str;
use Carbon\Carbon;

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

  public function add(array $values) {
    $buckets = json_decode($values['buckets'], true);

    $info = BucketSimInfo::create([
      'uuid' => Str::uuid()->toString(),
      'description' => $values['description'],
    ]);

    foreach ($buckets as &$bucket) {
      $bucket['id_sim_info'] = $info->id;
      $bucket['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
      $bucket['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
    }

    unset($bucket);

    BucketSim::insert($buckets);
  }
}
