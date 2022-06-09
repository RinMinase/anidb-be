<?php

namespace App\Repositories;

use App\Models\Entry;

class EntryRepository {

  public function getAll() {
    return Entry::all();
  }

  public function get($id) {
    return Entry::whereId($id)->get();
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
