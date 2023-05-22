<?php

namespace App\Resources\Entry;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use Carbon\CarbonInterval;

/**
 * @OA\Schema(
 *   schema="Entry",
 *   example={
 *     "id": "e9597119-8452-4f2b-96d8-f2b1b1d2f158",
 *     "quality": "4K 2160p",
 *     "title": "Sample Title",
 *     "dateInitFinishedRaw": "2001-01-01T00:00:00.000000Z",
 *     "dateInitFinished": "Jan 01, 2001",
 *     "dateLastFinished": "Mar 01, 2011",
 *     "durationRaw": 10000,
 *     "duration": "2 hours 46 minutes 40 seconds",
 *     "filesizeRaw": 21331439861,
 *     "filesize": "19.87 GB",
 *
 *     "episodes": 25,
 *     "ovas": 1,
 *     "specials": 1,
 *
 *     "seasonNumber": 1,
 *     "seasonFirstTitle": "Sample Title",
 *
 *     "prequelTitle": "Prequel Title",
 *     "prequel": {
 *       "id": "9b712dbe-ff81-41d4-b027-ee87cea4da99",
 *       "title": "Prequel Title",
 *     },
 *     "sequelTitle": "Sequel Title",
 *     "sequel": {
 *       "id": "08d0f87f-6432-47bc-b6bd-c856abbad90f",
 *       "title": "Sequel Title",
 *     },
 *
 *     "encoder": "encoder-encoder2-encoder3",
 *     "encoderVideo": "encoder",
 *     "encoderAudio": "encoder2",
 *     "encoderSubs": "encoder3",
 *
 *     "releaseSeason": "Winter",
 *     "releaseYear": 2000,
 *     "release": "Winter 2000",
 *
 *     "variants": "Variant title",
 *     "remarks": "Some remarks",
 *
 *     "codecHDR": 1,
 *     "id_codec_video": 1,
 *     "codecVideo": "x264 8bit",
 *     "id_codec_audio": 4,
 *     "codecAudio": "FLAC 7.1",
 *
 *     "offquels": {{
 *       "id": "89e3be00-9d4f-4c4f-a99f-c12cbfba04ab",
 *       "title": "Offquel Title"
 *     }},
 *     "rewatches": {{
 *       "id": "af846c35-a51e-4534-9559-c75114e61d84",
 *       "dateIso": "2011-03-01T00:00:00.000000Z",
 *       "date": "March 01, 2011",
 *     }},
 *
 *     "ratingAverage": 4.5,
 *     "rating": {
 *       "audio": 6,
 *       "enjoyment": 5,
 *       "graphics": 4,
 *       "plot": 3
 *     },
 *     "image": "{{ image url }}",
 *    },
 *   type="object",
 *   @OA\Property(property="id", type="string", format="uuid"),
 *   @OA\Property(
 *     property="quality",
 *     type="string",
 *     enum={"4K 2160", "FHD 1080p", "HD 720p", "HQ 480p", "LQ 360p"}
 *   ),
 *   @OA\Property(property="title", type="string"),
 *   @OA\Property(property="dateInitFinishedRaw", type="string", format="date-time"),
 *   @OA\Property(property="dateInitFinished", type="string", format="date-time"),
 *   @OA\Property(property="dateLastFinished", type="string", format="date-time"),
 *   @OA\Property(property="durationRaw", type="integer", format="int64"),
 *   @OA\Property(property="duration", type="string"),
 *   @OA\Property(property="filesizeRaw", type="integer", format="int64"),
 *   @OA\Property(property="filesize", type="string"),
 *
 *   @OA\Property(property="episodes", type="integer", format="int32"),
 *   @OA\Property(property="ovas", type="integer", format="int32"),
 *   @OA\Property(property="specials", type="integer", format="int32"),
 *
 *   @OA\Property(property="seasonNumber", type="integer", format="int32"),
 *   @OA\Property(property="seasonFirstTitle", type="string"),
 *
 *   @OA\Property(property="prequelTitle", type="string"),
 *   @OA\Property(
 *     property="prequel",
 *     type="object",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="title", type="string"),
 *   ),
 *
 *   @OA\Property(property="sequelTitle", type="string"),
 *   @OA\Property(
 *     property="sequel",
 *     type="object",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="title", type="string"),
 *   ),
 *
 *   @OA\Property(property="encoder", type="string"),
 *   @OA\Property(property="encoderVideo", type="string"),
 *   @OA\Property(property="encoderAudio", type="string"),
 *   @OA\Property(property="encoderSubs", type="string"),
 *
 *   @OA\Property(
 *     property="releaseSeason",
 *     type="string",
 *     enum={"Winter", "Spring", "Summer", "Fall"}
 *   ),
 *   @OA\Property(property="releaseYear", type="integer", format="int32"),
 *   @OA\Property(property="release", type="string"),
 *
 *   @OA\Property(property="variants", type="string"),
 *   @OA\Property(property="remarks", type="string"),
 *
 *   @OA\Property(property="codecHDR", type="integer", format="int32"),
 *   @OA\Property(property="id_codec_video", type="integer", format="int32"),
 *   @OA\Property(property="codecVideo", type="string"),
 *   @OA\Property(property="id_codec_audio", type="integer", format="int32"),
 *   @OA\Property(property="codecAudio", type="string"),
 *
 *   @OA\Property(
 *     property="offquels",
 *     type="array",
 *     @OA\Items(
 *       @OA\Property(property="id", type="string", format="uuid"),
 *       @OA\Property(property="title", type="string"),
 *     ),
 *   ),
 *   @OA\Property(
 *     property="rewatches",
 *     type="array",
 *     @OA\Items(
 *       @OA\Property(property="id", type="string", format="uuid"),
 *       @OA\Property(property="dateIso", type="string", format="date-time"),
 *       @OA\Property(property="date", type="string"),
 *     ),
 *   ),
 *
 *   @OA\Property(property="ratingAverage", type="number"),
 *   @OA\Property(
 *     property="rating",
 *     type="object",
 *     @OA\Property(property="audio", type="integer", format="int32"),
 *     @OA\Property(property="enjoyment", type="integer", format="int32"),
 *     @OA\Property(property="graphics", type="integer", format="int32"),
 *     @OA\Property(property="plot", type="integer", format="int32"),
 *   ),
 *   @OA\Property(property="image", type="string", format="uri"),
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

      'offquels' => EntryOffquelCollection::collection($this->offquels),
      'rewatches' => EntryRewatchCollection::collection($this->rewatches),

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
