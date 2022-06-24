<?php

namespace App\Repositories;

use App\Models\Bucket;

class BucketRepository {

  public function getAll() {
    return Bucket::all();
  }

  public function get($id) {
    return Bucket::where('id', $id)->first();
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
}
