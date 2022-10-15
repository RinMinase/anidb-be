<?php

namespace App\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Str;

use App\Models\Group;

class GroupRepository {

  public function getAll() {
    return Group::orderBy('name')->get();
  }

  public function add(array $values) {
    return Group::create([
      'uuid' => Str::uuid()->toString(),
      'name' => $values['name'],
    ]);
  }

  public function edit(array $values, $uuid) {
    return Group::where('uuid', $uuid)->update($values);
  }

  public function delete($uuid) {
    return Group::where('uuid', $uuid)
      ->firstOrFail()
      ->delete();
  }

  public function import(array $values) {
    $import = [];

    if (!empty($values)) {
      foreach ($values as $item) {
        $data = [
          'uuid' => Str::uuid()->toString(),
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
