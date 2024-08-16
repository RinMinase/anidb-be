<?php

namespace App\Models;

class BucketSim extends BaseModel {

  protected $fillable = [
    'id_sim_info',
    'from',
    'to',
    'size',
  ];

  protected $hidden = [
    'id',
    'created_at',
    'updated_at',
  ];

  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:i:s',
  ];

  public function info() {
    return $this->belongsTo(BucketSimInfo::class, 'id_sim_info');
  }
}
