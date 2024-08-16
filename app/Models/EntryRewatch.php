<?php

namespace App\Models;

class EntryRewatch extends BaseModel {

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

  protected $casts = [];
}
