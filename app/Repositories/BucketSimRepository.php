<?php

namespace App\Repositories;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Str;

use App\Exceptions\JsonParsingException;
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
    Bucket::refreshAutoIncrements();

    $this->delete($uuid);
  }

  public function clone(string $uuid) {
    $info = BucketSimInfo::where('uuid', $uuid)->firstOrFail();
    $buckets = BucketSim::where('id_sim_info', $info->id)->get()->toArray();

    $new_id = Str::uuid()->toString();
    $cloned_info = $info->replicate(['id', 'uuid'])->toArray();
    $cloned_info['uuid'] = $new_id;
    $cloned_info['description'] = $cloned_info['description'] . ' - cloned';

    BucketSimInfo::create($cloned_info);

    $new_info = BucketSimInfo::where('uuid', $new_id)->firstOrFail();

    foreach ($buckets as $bucket) {
      $bucket['id_sim_info'] = $new_info->id;
      BucketSim::create($bucket);
    }

    return $new_id;
  }

  public function preview(array $values) {
    $raw_buckets = json_decode($values['buckets'], true);

    $buckets = [];

    // Verify JSON partially
    foreach ($raw_buckets as $value) {
      $from = $value['from'] ?? null;
      $to = $value['to'] ?? null;
      $size = $value['size'] ?? null;

      if (!$from || strlen($from) > 1 || !ctype_alpha($from)) {
        throw new JsonParsingException();
      }

      if (!$to || strlen($to) > 1 || !ctype_alpha($to)) {
        throw new JsonParsingException();
      }

      if (!$size || !ctype_digit($size) || $size <= 0) {
        throw new JsonParsingException();
      }

      $from = strtolower($from);
      $to = strtolower($to);

      if (strcmp($from, $to) > 0) {
        throw new JsonParsingException();
      }
    }

    foreach ($raw_buckets as $value) {
      $new_bucket = new Bucket($value);
      array_push($buckets, $new_bucket);
    }

    $buckets = collect($buckets);

    try {
      $entryRepo = new EntryRepository();
      $preview_data = $entryRepo->getBuckets($buckets);
    } catch (Exception $e) {
      throw new JsonParsingException();
    }

    return $preview_data;
  }
}
