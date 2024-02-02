<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *   example={
 *     "uuid": "e9597119-8452-4f2b-96d8-f2b1b1d2f158",
 *     "title": "Item 4",
 *     "link": "{{ rss item link }}",
 *     "guid": "{{ rss item guid link }}",
 *     "date": "2022-01-04 00:01:00",
 *     "is_read": false,
 *     "is_bookmarked": true,
 *     "created_at": "2023-05-21 21:05:57"
 *   },
 *   @OA\Property(property="uuid", type="string", format="uuid"),
 *   @OA\Property(property="title", type="string"),
 *   @OA\Property(property="link", type="string", format="uri"),
 *   @OA\Property(property="guid", type="string", format="uri"),
 *   @OA\Property(property="date", type="string"),
 *   @OA\Property(property="is_read", type="boolean"),
 *   @OA\Property(property="is_bookmarked", type="boolean"),
 *   @OA\Property(property="created_at", type="string"),
 * )
 */
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
    'id_rss',
    'updated_at',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:i:s',
    'date' => 'datetime:Y-m-d H:i:s',
  ];

  public function rss() {
    return $this->belongsTo(Rss::class, 'id_rss');
  }
}
