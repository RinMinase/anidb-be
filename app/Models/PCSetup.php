<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Traits\RefreshableAutoIncrements;

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

  protected function casts(): array {
    return [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
      'deleted_at' => 'datetime:Y-m-d H:i:s',
    ];
  }

  public function owner() {
    return $this->belongsTo(PCOwner::class, 'id_owner');
  }

  public function info() {
    return $this->belongsTo(PCInfo::class, 'id_info');
  }

  public function component() {
    return $this->belongsTo(PCComponent::class, 'id_component');
  }
}
