<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Traits\RefreshableAutoIncrements;

class Partial extends Model {

  use RefreshableAutoIncrements;

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

  protected function casts(): array {
    return [
      'created_at' => 'datetime:Y-m-d H:i:s',
    ];
  }

  public function catalog() {
    return $this->belongsTo(Catalog::class, 'id_catalog');
  }

  public function priority() {
    return $this->belongsTo(Priority::class, 'id_priority');
  }
}
