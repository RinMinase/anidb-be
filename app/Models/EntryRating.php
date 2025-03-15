<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Traits\RefreshableAutoIncrements;

class EntryRating extends Model {

  use RefreshableAutoIncrements, SoftDeletes;

  protected $table = 'entries_rating';

  protected $fillable = [
    'id_entries',
    'audio',
    'enjoyment',
    'graphics',
    'plot',
  ];

  protected $hidden = [
    'id',
    'id_entries',
    'created_at',
    'updated_at',
    'deleted_at',
  ];
}
