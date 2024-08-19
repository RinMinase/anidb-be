<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\RefreshableAutoIncrements;

class Rss extends Model {

  use RefreshableAutoIncrements;

  protected $table = 'rss';

  protected $fillable = [
    'uuid',
    'title',
    'last_updated_at',
    'update_speed_mins',
    'url',
    'max_items',
  ];

  protected $hidden = [
    'id',
    'updated_at',
  ];

  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:i:s',
  ];

  public function items() {
    return $this->hasMany(RssItem::class, 'id_rss');
  }
}
