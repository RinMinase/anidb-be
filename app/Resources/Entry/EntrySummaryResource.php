<?php

namespace App\Resources\Entry;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

/**
 * @OA\Schema(
 *   @OA\Property(
 *     property="id",
 *     type="string",
 *     format="uuid",
 *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
 *   ),
 *   @OA\Property(
 *     property="quality",
 *     type="string",
 *     enum={"4K 2160", "FHD 1080p", "HD 720p", "HQ 480p", "LQ 360p"},
 *     example="4K 2160p",
 *   ),
 *   @OA\Property(property="title", type="string", example="Sample Title"),
 *   @OA\Property(property="dateFinished", type="string", example="Mar 01, 2011"),
 *   @OA\Property(property="rewatched", type="boolean", example=false),
 *   @OA\Property(property="filesize", type="string", example="10.25 GB"),
 *   @OA\Property(property="episodes", type="integer", format="int32", example=25),
 *   @OA\Property(property="ovas", type="integer", format="int32", example=1),
 *   @OA\Property(property="specials", type="integer", format="int32", example=1),
 *   @OA\Property(property="encoder", type="string", example="encoder—encoder2"),
 *   @OA\Property(property="release", type="string", example="Spring 2017"),
 *   @OA\Property(property="remarks", type="string", example="Some remarks"),
 *   @OA\Property(property="rating", type="number", example=7.5),
 * ),
 */
class EntrySummaryResource extends JsonResource {

  public function toArray($request) {

    return [
      'id' => $this->uuid,
      'quality' => $this->quality->quality,
      'title' => $this->title,
      'dateFinished' => $this->calcDateFinished(),
      'rewatched' => $this->calcRewatched(),
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

    return join(' — ', $encoders);
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

  private function calcRewatched() {
    return (bool)count($this->rewatches);
  }
}
