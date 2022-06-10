<?php

namespace App\Repositories;

use App\Models\Entry;

class EntryRepository {

  public function getAll() {
    return Entry::join('qualities', 'entries.id_quality', '=', 'qualities.id')
      ->leftJoin('entries_rating', 'entries.id', '=', 'entries_rating.id_entries')
      ->select(
        'entries.*',
        'qualities.quality',
        'entries_rating.audio as rating_audio',
        'entries_rating.enjoyment as rating_enjoyment',
        'entries_rating.graphics as rating_graphics',
        'entries_rating.plot as rating_plot',
      )->get();
  }

  public function get($id) {
    return Entry::where('entries.id', $id)
      ->select(
        'entries.*',
        'qualities.quality',
        'entries_rating.audio as rating_audio',
        'entries_rating.enjoyment as rating_enjoyment',
        'entries_rating.graphics as rating_graphics',
        'entries_rating.plot as rating_plot',
      )->join('qualities', 'entries.id_quality', '=', 'qualities.id')
      ->leftJoin('entries_rating', 'entries.id', '=', 'entries_rating.id_entries')
      ->get();
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
