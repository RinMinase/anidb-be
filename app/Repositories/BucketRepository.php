<?php

namespace App\Repositories;

use App\Models\Bucket;

class BucketRepository {

  public function getAll() {
    return Bucket::all();
  }
}
