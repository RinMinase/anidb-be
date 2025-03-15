<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Traits\RefreshableAutoIncrements;

class EntryRewatch extends Model {

  use RefreshableAutoIncrements;

  protected $table = 'entries_rewatch';

  protected $fillable = [
    'uuid',
    'id_entries',
    'date_rewatched',
  ];

  protected $hidden = [
    'id',
    'id_entries',
  ];
}
