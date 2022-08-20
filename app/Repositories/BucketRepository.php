<?php

namespace App\Repositories;

use Carbon\Carbon;

use App\Models\Bucket;

class BucketRepository {

  public function getAll() {
    return Bucket::all();
  }

  public function add(array $values) {
    return Bucket::create($values);
  }

  public function edit(array $values, $id) {
    return Bucket::where('id', $id)->update($values);
  }

  public function delete($id) {
    return Bucket::where('id', $id)
      ->firstOrFail()
      ->delete();
  }

  public function import(array $values) {
    $import = [];

    foreach ($values as $item) {
      if (!empty($item)) {
        $data = [
          'from' => $item->from,
          'to' => $item->to,
          'size' => $item->size,

          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ];

        array_push($import, $data);
      }
    }

    Bucket::insert($import);

    return count($import);
  }
}
