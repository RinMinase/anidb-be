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
    'label',
    'is_active',
    'id_owner',
  ];

  protected $hidden = [
    'id',
  ];

  protected $casts = [];
}
