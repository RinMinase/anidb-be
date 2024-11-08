<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\RefreshableAutoIncrements;

class EntryOffquel extends Model {

  use RefreshableAutoIncrements;

  protected $table = 'entries_offquel';
  public $timestamps = null;

  protected $fillable = [
    'id_entries',
    'id_entries_offquel',
  ];

  protected $hidden = [
    'id_entries',
  ];

  protected $casts = [];

  public function entry() {
    return $this->belongsTo(Entry::class, 'id_entries_offquel');
  }
}
