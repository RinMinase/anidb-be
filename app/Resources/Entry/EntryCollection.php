<?php

namespace App\Resources\Entry;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class EntryCollection extends JsonResource {

  public function toArray($request) {

    /**
     * Rating Computation
     */
    $rating = 0;
    $rating += $this->rating ? $this->rating->audio : 0;
    $rating += $this->rating ? $this->rating->enjoyment : 0;
    $rating += $this->rating ? $this->rating->graphics : 0;
    $rating += $this->rating ? $this->rating->plot : 0;
    $rating = round($rating / 4, 2);

    /**
     * Last Date Finished Computation
     */
    $last_date_finsihed = '';

    if ($this->date_finished) {
      $last_date_finsihed = Carbon::parse($this->date_finished)
        ->format('M d, Y');
    }

    if (count($this->rewatches)) {
      $len = count($this->rewatches);
      $last_date = $this->rewatches[$len - 1]->date_rewatched;
      $last_date_finsihed = Carbon::parse($last_date)
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
     * Encoder Computation
     */
    $encoders = [];

    if ($this->encoder_video) $encoders[] = $this->encoder_video;
    if ($this->encoder_audio) $encoders[] = $this->encoder_audio;
    if ($this->encoder_subs) $encoders[] = $this->encoder_subs;

    $encoder = join('—', $encoders);

    /**
     * Release Computation
     */
    $release = trim($this->release_season . ' ' . $this->release_year);

    return [
      'id' => $this->id,
      'quality' => $this->quality->quality,
      'title' => $this->title,
      'dateFinished' => $last_date_finsihed,
      'filesize' => $filesize,
      'episodes' => $this->episodes,
      'ovas' => $this->ovas,
      'specials' => $this->specials,
      'encoder' => $encoder,
      'release' => $release,
      'remarks' => $this->remarks,
      'rating' => $rating,
    ];
  }
}
