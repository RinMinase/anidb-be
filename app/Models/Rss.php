<?php

namespace App\Models;

class Rss extends BaseModel {

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
