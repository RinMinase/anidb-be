<?php

namespace App\Resources\Entry;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use Carbon\CarbonInterval;

class EntryResource extends JsonResource {

  public function toArray($request) {

    return [
      'id' => $this->uuid,
      'quality' => $this->quality->quality,
      'id_quality' => $this->quality->id,
      'title' => $this->title,
      'dateInitFinished' => $this->calcDateInitFinish(),
      'dateLastFinished' => $this->calcDateLastFinish(),
      'duration' => $this->calcDuration(),
      'filesize' => parse_filesize($this->filesize ?? 0),

      'episodes' => $this->episodes ?? 0,
      'ovas' => $this->ovas ?? 0,
      'specials' => $this->specials ?? 0,

      'seasonNumber' => $this->season_number,
      'seasonFirstTitle' => $this->season_first_title->title ?? '',

      'prequel' => $this->prequel->title ?? '',
      'sequel' => $this->sequel->title ?? '',

      'encoder' => $this->calcEncoder(),
      'encoderVideo' => $this->encoder_video,
      'encoderAudio' => $this->encoder_audio,
      'encoderSubs' => $this->encoder_subs,

      'releaseSeason' => $this->release_season, // for icon
      'release' => $this->calcRelease(),

      'variants' => $this->variants,
      'remarks' => $this->remarks,

      'codecHDR' => $this->codec_hdr,
      'codecVideo' => $this->codec_video->codec,
      'codecAudio' => $this->codec_audio->codec,

      'offquels' => EntryOffquelCollection::collection($this->offquels),
      'rewatches' => EntryRewatchCollection::collection($this->rewatches),

      'ratingAverage' => $this->calcRating(),
      'rating' => $this->rating,
    ];
  }

  private function calcDateInitFinish() {
    $initial_date_finished = '';

    if ($this->date_finished) {
      $initial_date_finished = Carbon::parse($this->date_finished)
        ->format('M d, Y');
    }

    return $initial_date_finished;
  }

  private function calcDateLastFinish() {
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

  private function calcDuration() {
    return CarbonInterval::seconds($this->duration ?? 0)
      ->cascade()
      ->forHumans();
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
