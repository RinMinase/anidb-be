<?php

namespace App\Models;

class Partial extends BaseModel {

  protected $fillable = [
    'uuid',
    'title',
    'id_catalog',
    'id_priority',
  ];

  protected $hidden = [
    'id',
    'created_at',
    'updated_at',
    'deleted_at',
  ];

  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:i:s',
  ];

  public function priority() {
    return $this->belongsTo(Priority::class, 'id_priority');
  }
}
