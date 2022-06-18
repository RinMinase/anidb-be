<?php

namespace App\Resources\Entry;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class EntryCollection extends JsonResource {

  public function toArray($request) {

    return [
      'id' => $this->id,
      'quality' => $this->quality->quality,
      'title' => $this->title,
      'dateFinished' => $this->calcDateFinished(),
      'filesize' => parse_filesize($this->filesize ?? 0),
      'episodes' => $this->episodes ?? 0,
      'ovas' => $this->ovas ?? 0,
      'specials' => $this->specials ?? 0,
      'encoder' => $this->calcEncoder(),
      'release' => $this->calcRelease(),
      'remarks' => $this->remarks,
      'rating' => $this->calcRating(),
    ];
  }

  private function calcDateFinished() {
    $last_date_finished = '';

    if ($this->date_finished) {
      $last_date_finished = Carbon::parse($this->date_finished)
        ->format('M d, Y');
    }

    if (count($this->rewatches)) {
      $len = count($this->rewatches);
      $last_date = $this->rewatches[$len - 1]->date_rewatched;
      $last_date_finished = Carbon::parse($last_date)
        ->format('M d, Y');
    }

    return $last_date_finished;
  }

  private function calcEncoder() {
    $encoders = [];

    if ($this->encoder_video) $encoders[] = $this->encoder_video;
    if ($this->encoder_audio) $encoders[] = $this->encoder_audio;
    if ($this->encoder_subs) $encoders[] = $this->encoder_subs;

    return join('—', $encoders);
  }

  private function calcRating() {
    $rating = 0;
    $rating += $this->rating->audio ?? 0;
    $rating += $this->rating->enjoyment ?? 0;
    $rating += $this->rating->graphics ?? 0;
    $rating += $this->rating->plot ?? 0;
    $rating = round($rating / 4, 2);

    return $rating;
  }

  private function calcRelease() {
    return trim($this->release_season . ' ' . $this->release_year);
  }
}
