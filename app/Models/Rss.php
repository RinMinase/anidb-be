<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rss extends Model {

  protected $table = 'rss';

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'title',
    'last_updated_at',
    'update_speed_mins',
    'url',
    'max_items',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'id',
    'updated_at',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:m:s',
  ];

  public function items() {
    return $this->hasMany(RssItem::class, 'id_rss');
  }
}
