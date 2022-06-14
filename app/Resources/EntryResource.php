<?php

namespace App\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EntryResource extends JsonResource {

  public function toArray($request) {
    $rating = 0;
    $rating += $this->rating_audio;
    $rating += $this->rating_enjoyment;
    $rating += $this->rating_graphics;
    $rating += $this->rating_plot;
    $rating = round($rating / 4, 2);

    return [
      'quality' => $this->quality,
      'title' => $this->title,
      'date_finished' => $this->date_finished,
      'duration' => $this->duration,
      'filesize' => $this->filesize,
      'episodes' => $this->episodes,
      'ovas' => $this->ovas,
      'specials' => $this->specials,
      'season_number' => $this->season_number,
      'season_first_title' => $this->season_first_title,
      'prequel' => $this->prequel,
      'sequel' => $this->sequel,
      'encoder_video' => $this->encoder_video,
      'encoder_audio' => $this->encoder_audio,
      'encoder_subs' => $this->encoder_subs,
      'release_year' => $this->release_year,
      'release_season' => $this->release_season,
      'variants' => $this->variants,
      'remarks' => $this->remarks,
      'offquels' => $this->offquels,
      'rewatches' => $this->rewatches,
      'rating' => $rating,
    ];
  }
}