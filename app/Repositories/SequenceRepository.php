<?php

namespace App\Repositories;

use Carbon\Carbon;

use App\Models\Sequence;

class SequenceRepository {

  public function getAll() {
    return Sequence::orderBy('date_from', 'desc')->get();
  }

  public function get($id) {
    return Sequence::where('id', $id)->firstOrFail();
  }

  public function add(array $values) {
    return Sequence::create($values);
  }

  public function edit(array $values, $id) {
    return Sequence::where('id', $id)->update($values);
  }

  public function delete($id) {
    return Sequence::where('id', $id)
      ->firstOrFail()
      ->delete();
  }

  public function import($values) {
    $import = [];

    foreach ($values as $item) {
      if (!empty($item)) {
        $data = [
          'date_from' => Carbon::createFromTimestamp($item->timeStart)
            ->format('Y-m-d'),
          'date_to' => Carbon::createFromTimestamp($item->timeEnd)
            ->format('Y-m-d'),
          'title' => $item->title,

          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ];

        array_push($import, $data);
      }
    }

    Sequence::insert($import);

    return count($import);
  }
}
