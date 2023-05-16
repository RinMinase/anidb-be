<?php

namespace App\Resources\Rss;

use Illuminate\Http\Resources\Json\JsonResource;

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
