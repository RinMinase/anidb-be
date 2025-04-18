<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Traits\RefreshableAutoIncrements;

class EntryGenre extends Model {

  use RefreshableAutoIncrements;

  protected $table = 'entries_genre';
  public $timestamps = null;

  protected $fillable = [
    'id_entries',
    'id_genres',
  ];

  protected $hidden = [
    'id_entries',
  ];

  public function genre() {
    return $this->belongsTo(Genre::class, 'id_genres');
  }
}
