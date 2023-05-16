<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RssItem extends Model {

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'id_rss',
    'title',
    'link',
    'guid',
    'date',
    'is_read',
    'is_bookmarked',
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
    'date' => 'datetime:Y-m-d H:m:s',
  ];

  public function rss() {
    return $this->belongsTo(Rss::class, 'id_rss');
  }
}
