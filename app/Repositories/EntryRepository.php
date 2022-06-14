<?php

namespace App\Repositories;

use App\Models\Entry;

class EntryRepository {

  public function getAll() {
    return Entry::select()
      ->with('offquels')
      ->with('rewatches')
      ->with('rating')
      ->get();
  }

  public function get($id) {
    return Entry::where('entries.id', $id)
      ->with('offquels')
      ->with('rewatches')
      ->with('rating')
      ->first();
  }

  public function add(array $values) {
    return Entry::create($values);
  }

  public function edit(array $values, $id) {
    return Entry::whereId($id)->update($values);
  }

  public function delete($id) {
    return Entry::findOrFail($id)->delete();
  }
}
