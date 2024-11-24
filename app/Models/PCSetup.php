<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\RefreshableAutoIncrements;

class PCSetup extends Model {

  use RefreshableAutoIncrements, SoftDeletes;

  protected $table = 'pc_setups';

  protected $fillable = [
    'id',
    'id_owner',
    'id_info',
    'id_component',
    'count',
    'is_hidden',
  ];

  protected $hidden = [];

  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:i:s',
    'updated_at' => 'datetime:Y-m-d H:i:s',
    'deleted_at' => 'datetime:Y-m-d H:i:s',
  ];
}
