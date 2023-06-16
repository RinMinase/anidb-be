<?php

namespace App\Resources\Entry;

use Illuminate\Http\Resources\Json\JsonResource;

use Carbon\Carbon;
use Carbon\CarbonInterval;

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
 *   @OA\Property(
 *     property="dateInitFinishedRaw",
 *     type="string",
 *     format="date-time",
 *     example="2001-01-01T00:00:00.000000Z",
 *   ),
 *   @OA\Property(property="dateInitFinished", type="string", example="Jan 01, 2001"),
 *   @OA\Property(property="dateLastFinished", type="string", example="Mar 01, 2011"),
 *   @OA\Property(
 *     property="durationRaw",
 *     type="integer",
 *     format="int64",
 *     example=10000,
 *   ),
 *   @OA\Property(
 *     property="duration",
 *     type="string",
 *     example="2 hours 46 minutes 40 seconds",
 *   ),
 *   @OA\Property(
 *     property="filesizeRaw",
 *     type="integer",
 *     format="int64",
 *     example=21331439861,
 *   ),
 *   @OA\Property(property="filesize", type="string", example="19.87 GB"),
 *
 *   @OA\Property(property="episodes", type="integer", format="int32", example=25),
 *   @OA\Property(property="ovas", type="integer", format="int32", example=1),
 *   @OA\Property(property="specials", type="integer", format="int32", example=1),
 *
 *   @OA\Property(property="seasonNumber", type="integer", format="int32", example=1),
 *   @OA\Property(property="seasonFirstTitle", type="string", example="Sample Title"),
 *
 *   @OA\Property(property="prequelTitle", type="string", example="Prequel Title"),
 *   @OA\Property(
 *     property="prequel",
 *     @OA\Property(
 *       property="id",
 *       type="string",
 *       format="uuid",
 *       example="9b712dbe-ff81-41d4-b027-ee87cea4da99",
 *     ),
 *     @OA\Property(property="title", type="string", example="Prequel Title"),
 *   ),
 *   @OA\Property(property="sequelTitle", type="string", example="Sequel Title"),
 *   @OA\Property(
 *     property="sequel",
 *     @OA\Property(
 *       property="id",
 *       type="string",
 *       format="uuid",
 *       example="08d0f87f-6432-47bc-b6bd-c856abbad90f",
 *     ),
 *     @OA\Property(property="title", type="string", example="Sequel Title"),
 *   ),
 *
 *   @OA\Property(property="encoder", type="string", example="encoder-encoder2-encoder3"),
 *   @OA\Property(property="encoderVideo", type="string", example="encoder"),
 *   @OA\Property(property="encoderAudio", type="string", example="encoder2"),
 *   @OA\Property(property="encoderSubs", type="string", example="encoder3"),
 *
 *   @OA\Property(
 *     property="releaseSeason",
 *     type="string",
 *     enum={"Winter", "Spring", "Summer", "Fall"},
 *     example="Winter",
 *   ),
 *   @OA\Property(property="releaseYear", type="integer", format="int32", example=2000),
 *   @OA\Property(property="release", type="string", example="Winter 2000"),
 *
 *   @OA\Property(property="variants", type="string", example="Variant title"),
 *   @OA\Property(property="remarks", type="string", example="Some remarks"),
 *
 *   @OA\Property(property="codecHDR", type="integer", format="int32", example=1),
 *   @OA\Property(property="id_codec_video", type="integer", format="int32", example=1),
 *   @OA\Property(property="codecVideo", type="string", example="x264 8bit"),
 *   @OA\Property(property="id_codec_audio", type="integer", format="int32", example=4),
 *   @OA\Property(property="codecAudio", type="string", example="FLAC 7.1"),
 *
 *   @OA\Property(
 *     property="offquels",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/EntryOffquelResource"),
 *   ),
 *   @OA\Property(
 *     property="rewatches",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/EntryRewatchResource")
 *   ),
 *
 *   @OA\Property(property="ratingAverage", type="number", example=4.5),
 *   @OA\Property(
 *     property="rating",
 *     @OA\Property(property="audio", type="integer", format="int32", example=6),
 *     @OA\Property(property="enjoyment", type="integer", format="int32", example=5),
 *     @OA\Property(property="graphics", type="integer", format="int32", example=4),
 *     @OA\Property(property="plot", type="integer", format="int32", example=3),
 *   ),
 *   @OA\Property(property="image", type="string", format="uri", example="{{ image url }}"),
 * ),
 */
class EntryResource extends JsonResource {

  public function toArray($request) {

    return [
      'id' => $this->uuid,
      'quality' => $this->quality->quality,
      'id_quality' => $this->quality->id,
      'title' => $this->title,
      'dateInitFinishedRaw' => $this->calcDateInitFinishRaw(),
      'dateInitFinished' => $this->calcDateInitFinish(),
      'dateLastFinished' => $this->calcDateLastFinish(),
      'durationRaw' => $this->duration ?? 0,
      'duration' => $this->calcDuration(),
      'filesizeRaw' => $this->filesize ?? 0,
      'filesize' => parse_filesize($this->filesize),

      'episodes' => $this->episodes ?? 0,
      'ovas' => $this->ovas ?? 0,
      'specials' => $this->specials ?? 0,

      'seasonNumber' => $this->season_number,
      'seasonFirstTitle' => $this->season_first_title->title ?? '',

      'prequelTitle' => $this->prequel->title ?? '',
      'prequel' => $this->prequel ? [
        'id' => $this->prequel->uuid,
        'title' => $this->prequel->title,
      ] : null,

      'sequelTitle' => $this->sequel->title ?? '',
      'sequel' => $this->sequel ? [
        'id' => $this->sequel->uuid,
        'title' => $this->sequel->title,
      ] : null,

      'encoder' => $this->calcEncoder(),
      'encoderVideo' => $this->encoder_video,
      'encoderAudio' => $this->encoder_audio,
      'encoderSubs' => $this->encoder_subs,

      'releaseSeason' => $this->release_season, // for icon
      'releaseYear' => $this->release_year, // for icon
      'release' => $this->calcRelease(),

      'variants' => $this->variants,
      'remarks' => $this->remarks,

      'codecHDR' => $this->codec_hdr,
      'id_codec_video' => $this->id_codec_video,
      'codecVideo' => $this->codec_video->codec ?? '',
      'id_codec_audio' => $this->id_codec_audio,
      'codecAudio' => $this->codec_audio->codec ?? '',

      'offquels' => EntryOffquelResource::collection($this->offquels),
      'rewatches' => EntryRewatchResource::collection($this->rewatches),

      'ratingAverage' => $this->calcRating(),
      'rating' => $this->rating,
      'image' => $this->image,
    ];
  }

  private function calcDateInitFinishRaw() {
    $initial_date_finished = '';

    if ($this->date_finished) {
      $initial_date_finished = Carbon::parse($this->date_finished);
    }

    return $initial_date_finished;
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
}
