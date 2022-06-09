<?php

namespace App\Repositories;

use App\Models\Entry;

class EntryRepository {

  public function getAll() {
    return Entry::all();
  }

  public function get($id) {
    return Entry::where('id', $id)->get();
  }

  public function delete($id) {
    return Entry::findOrFail($id)->delete();
  }
}
