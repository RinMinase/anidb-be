<?php

namespace App\Repositories;

use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\Bucket;
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

    return [
      'data' => $entryRepo->getBuckets($buckets),
      'stats' => $info,
    ];
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

  public function edit(array $values, string $uuid) {
    $info = BucketSimInfo::where('uuid', $uuid)->firstOrFail();

    BucketSimInfo::where('uuid', $uuid)->update([
      'description' => $values['description'],
    ]);

    $buckets = json_decode($values['buckets'], true);

    BucketSim::where('id_sim_info', $info->id)->delete();

    foreach ($buckets as &$bucket) {
      $bucket['id_sim_info'] = $info->id;
      $bucket['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
      $bucket['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
    }

    unset($bucket);

    BucketSim::insert($buckets);
  }

  public function delete(string $uuid) {
    BucketSimInfo::where('uuid', $uuid)
      ->firstOrFail()
      ->delete();
  }

  public function save_bucket(string $uuid) {
    $info = BucketSimInfo::where('uuid', $uuid)->firstOrFail();

    $buckets = BucketSim::where('id_sim_info', $info->id)->get()->toArray();

    foreach ($buckets as &$bucket) {
      unset($bucket['id_sim_info']);

      $bucket['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
      $bucket['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
    }

    Bucket::truncate();
    Bucket::insert($buckets);

    refresh_db_table_autoincrement((new Bucket())->getTable());

    $this->delete($uuid);
  }
}
