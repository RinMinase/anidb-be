<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntryRewatch extends Model {

  protected $table = 'entries_rewatch';

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'id_entries',
    'date_rewatched',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'id',
    'id_entries',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [];
}
