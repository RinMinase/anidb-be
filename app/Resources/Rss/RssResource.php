<?php

namespace App\Resources\Rss;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   @OA\Property(
 *     property="uuid",
 *     type="string",
 *     format="uuid",
 *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
 *   ),
 *   @OA\Property(property="title", type="string", example="Sample RSS Feed"),
 *   @OA\Property(property="last_updated_at", type="string", example="2023-05-21 21:23:57"),
 *   @OA\Property(property="update_speed_mins", type="integer", format="int32", example=120),
 *   @OA\Property(property="url", type="string", format="uri", example="{{ rss url }}"),
 *   @OA\Property(property="max_items", type="integer", format="int32", example=250),
 *   @OA\Property(property="unread", type="integer", format="int32", example=3),
 *   @OA\Property(property="created_at", type="string", example="2023-05-21 21:05:57"),
 * )
 */
class RssResource extends JsonResource {

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
