<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\RefreshableAutoIncrements;

/**
 * @OA\Schema(
 *   example={
 *     "uuid": "e9597119-8452-4f2b-96d8-f2b1b1d2f158",
 *     "title": "Item 4",
 *     "link": "{{ rss item link }}",
 *     "guid": "{{ rss item guid link }}",
 *     "date": "2022-01-04 00:01:00",
 *     "isRead": false,
 *     "isBookmarked": true,
 *     "createdAt": "2023-05-21 21:05:57"
 *   },
 *   @OA\Property(property="uuid", type="string", format="uuid"),
 *   @OA\Property(property="title", type="string"),
 *   @OA\Property(property="link", type="string", format="uri"),
 *   @OA\Property(property="guid", type="string", format="uri"),
 *   @OA\Property(property="date", type="string"),
 *   @OA\Property(property="isRead", type="boolean"),
 *   @OA\Property(property="isBookmarked", type="boolean"),
 *   @OA\Property(property="createdAt", type="string"),
 * )
 */
class RssItem extends Model {

  use RefreshableAutoIncrements;

  protected $fillable = [
    'id_rss',
    'title',
    'link',
    'guid',
    'date',
    'is_read',
    'is_bookmarked',
  ];

  protected $hidden = [
    'id',
    'id_rss',
    'updated_at',
  ];

  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:i:s',
    'date' => 'datetime:Y-m-d H:i:s',
  ];

  public function rss() {
    return $this->belongsTo(Rss::class, 'id_rss');
  }
}
