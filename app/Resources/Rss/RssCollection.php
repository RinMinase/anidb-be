<?php

namespace App\Resources\Rss;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   example={{
 *     "uuid": "e9597119-8452-4f2b-96d8-f2b1b1d2f158",
 *     "title": "Sample RSS Feed",
 *     "last_updated_at": "2023-05-21 21:23:57",
 *     "update_speed_mins": 120,
 *     "url": "{{ rss url }}",
 *     "max_items": 250,
 *     "unread": 3,
 *     "created_at": "2023-05-21 21:05:57",
 *   }},
 *   type="array",
 *   @OA\Items(
 *     @OA\Property(property="uuid", type="string", format="uuid"),
 *     @OA\Property(property="title", type="string"),
 *     @OA\Property(property="last_updated_at", type="string"),
 *     @OA\Property(property="update_speed_mins", type="integer", format="int32"),
 *     @OA\Property(property="url", type="string", format="uri"),
 *     @OA\Property(property="max_items", type="integer", format="int32"),
 *     @OA\Property(property="unread", type="integer", format="int32"),
 *     @OA\Property(property="created_at", type="string"),
 *   ),
 * )
 */
class RssCollection extends JsonResource {

  public function toArray($request) {

    return [
      'uuid' => $this->uuid,

      'title' => $this->title,
      'last_updated_at' => $this->last_updated_at,
      'update_speed_mins' => $this->update_speed_mins,
      'url' => $this->url,
      'max_items' => $this->max_items,

      'unread' => $this->unread,

      'created_at' => $this->created_at,
    ];
  }
}
