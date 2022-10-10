<?php

namespace App\Repositories;

use Carbon\Carbon;

use App\Models\Group;

class GroupRepository {

  public function getAll() {
    return Group::orderBy('id')->pluck('name');
  }

  public function add(array $values) {
    return Group::create($values);
  }

  public function delete($id) {
    return Group::where('id', $id)
      ->firstOrFail()
      ->delete();
  }

  public function import(array $values) {
    $import = [];

    if (!empty($values)) {
      foreach ($values as $item) {
        $data = [
          'name' => $item,
          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ];

        array_push($import, $data);
      }
    }

    Group::truncate();
    Group::insert($import);

    return count($import);
  }
}
