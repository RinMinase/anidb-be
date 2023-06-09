<?php

namespace App\Resources\Anilist;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   @OA\Property(
 *     property="url",
 *     type="string",
 *     format="uri",
 *     example="https://example.com/",
 *   ),
 *   @OA\Property(property="title", type="string", example="Sample Title"),
 *   @OA\Property(property="episodes", type="integer", format="int32", example=100),
 *   @OA\Property(property="premiered", type="string", example="Winter 2020"),
 * )
 */
class AnilistTitleResource extends JsonResource {

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
