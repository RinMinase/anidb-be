<?php

namespace App\Repositories;

use Carbon\Carbon;

use App\Models\Bucket;

class BucketRepository {

  public function getAll() {
    return Bucket::all();
  }

  public function get($id) {
    return Bucket::where('id', $id)
      ->firstOrFail();
  }

  public function add(array $values) {
    return Bucket::create($values);
  }

  public function edit(array $values, $id) {
    return Bucket::where('id', $id)
      ->firstOrFail()
      ->update($values);
  }

  public function delete($id) {
    return Bucket::where('id', $id)
      ->firstOrFail()
      ->delete();
  }

  public function import(array $file) {
    $import = [];

    foreach ($file as $item) {
      if (!empty($item)) {
        $data = [
          'from' => $item->from ?? 'a',
          'to' => $item->to ?? 'a',
          'size' => $item->size ?? 0,

          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ];

        array_push($import, $data);
      }
    }

    Bucket::truncate();
    Bucket::insert($import);
    Bucket::refreshAutoIncrements();

    return count($import);
  }
}
