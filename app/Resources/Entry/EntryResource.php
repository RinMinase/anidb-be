<?php

namespace App\Resources\Entry;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use Carbon\CarbonInterval;

class EntryResource extends JsonResource {

  public function toArray($request) {

    /**
     * Rating Computation
     */
    $rating = 0;
    $rating += $this->rating->audio;
    $rating += $this->rating->enjoyment;
    $rating += $this->rating->graphics;
    $rating += $this->rating->plot;
    $rating = round($rating / 4, 2);

    /**
     * Encoder Computation
     */
    $encoders = [];

    if ($this->encoder_video) $encoders[] = $this->encoder_video;
    if ($this->encoder_audio) $encoders[] = $this->encoder_audio;
    if ($this->encoder_subs) $encoders[] = $this->encoder_subs;

    $encoder = join('—', $encoders);

    /**
     * Date Finished Computation
     */
    $initial_date_finished = '';

    if ($this->date_finished) {
      $initial_date_finished = Carbon::parse($this->date_finished)
        ->format('M d, Y');
    }

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

    /**
     * Filesize Computation
     */
    $KB = 1024;
    $MB = 1024 * $KB;
    $GB = 1024 * $MB;

    if ($this->filesize >= $GB) {
      $filesize = round($this->filesize / $GB, 2) . " GB";
    } else if ($this->filesize >= $MB) {
      $filesize = round($this->filesize / $MB, 2) . " MB";
    } else if ($this->filesize >= $KB) {
      $filesize = round($this->filesize / $KB, 2) . " KB";
    } else {
      $filesize = $this->filesize ?? 0 . " B";
    }

    /**
     * Release Computation
     */
    $release = trim($this->release_season . ' ' . $this->release_year);


    /**
     * Duration Computation
     */
    $duration = CarbonInterval::seconds($this->duration ?? 0)
      ->cascade()
      ->forHumans();


    return [
      'quality' => $this->quality->quality,
      'title' => $this->title,
      'initial_date_finished' => $initial_date_finished,
      'last_date_finished' => $last_date_finished,
      'duration' => $duration,
      'filesize' => $filesize,
      'episodes' => $this->episodes,
      'ovas' => $this->ovas,
      'specials' => $this->specials,
      'season_number' => $this->season_number,
      'season_first_title' => $this->season_first_title->title ?? '',
      'prequel' => $this->prequel->title ?? '',
      'sequel' => $this->sequel->title ?? '',
      'encoder' => $encoder,
      'encoder_video' => $this->encoder_video,
      'encoder_audio' => $this->encoder_audio,
      'encoder_subs' => $this->encoder_subs,
      'release_season' => $this->release_season, // for icon
      'release' => $release,
      'variants' => $this->variants,
      'remarks' => $this->remarks,
      'offquels' => EntryOffquelCollection::collection($this->offquels),
      'rewatches' => EntryRewatchCollection::collection($this->rewatches),
      'rating' => $rating,
    ];
  }
}
