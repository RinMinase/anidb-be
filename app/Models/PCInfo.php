<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\RefreshableAutoIncrements;

class PCInfo extends Model {

  use RefreshableAutoIncrements, SoftDeletes;

  protected $table = 'pc_infos';
  public $timestamps = null;

  protected $fillable = [
    'uuid',
    'id_owner',
    'label',
    'is_active',
    'is_hidden',
  ];

  protected $hidden = [
    'id',
  ];

  protected $casts = [];
}
