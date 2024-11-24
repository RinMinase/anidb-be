<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\RefreshableAutoIncrements;

class PCComponent extends Model {

  use RefreshableAutoIncrements, SoftDeletes;

  protected $table = 'pc_components';

  protected $fillable = [
    'id',
    'id_type',
    'name',
    'description',
    'price',
    'purchase_date',
    'purchase_location',
    'purchase_notes',
    'is_onhand',
  ];

  protected $hidden = [];

  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:i:s',
    'updated_at' => 'datetime:Y-m-d H:i:s',
    'deleted_at' => 'datetime:Y-m-d H:i:s',
  ];
}
