<?php

namespace App\Resources\Anilist;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   example={
 *     "url": "https://example.com/",
 *     "title": "Sample Title",
 *     "episodes": 100,
 *     "premiered": "Winter 2020",
 *   },
 *   @OA\Property(property="url", type="string", format="uri"),
 *   @OA\Property(property="title", type="string"),
 *   @OA\Property(property="episodes", type="integer", format="int32"),
 *   @OA\Property(property="premiered", type="string"),
 * )
 */
class AnilistResource extends JsonResource {

  public function toArray($request) {

    return [
      'url' => $this['siteUrl'] ?? '',
      'title' => $this->parseTitle(),
      'episodes' => $this['episodes'] ?? 0,
      'premiered' => $this->parsePremiered(),
    ];
  }

  private function parseTitle() {
    $title = '';

    if (isset($this['title']) && !empty($this['title']['romaji'])) {
      $title = $this['title']['romaji'];
    }

    return $title;
  }

  private function parsePremiered() {
    $premiered = '';

    if ($this['season'] && $this['seasonYear']) {
      $season = strtolower($this['season']);
      $season = ucwords($season);

      $premiered = $season . ' ' . $this['seasonYear'];
    }

    return $premiered;
  }
}
