<?php

namespace App\Requests\Entry;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rules\Enum;

use App\Enums\SeasonsEnum;

class EditRequest extends FormRequest {

  /**
   * @OA\Parameter(
   *   parameter="entry_edit_id_quality",
   *   name="id_quality",
   *   in="query",
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   * @OA\Parameter(
   *   parameter="entry_edit_title",
   *   name="title",
   *   in="query",
   *   @OA\Schema(type="string", minLength=1, maxLength=256),
   * ),
   *
   * @OA\Parameter(
   *   parameter="entry_edit_date_finished",
   *   name="date_finished",
   *   in="query",
   *   @OA\Schema(type="string", format="date"),
   * ),
   * @OA\Parameter(
   *   parameter="entry_edit_duration",
   *   name="duration",
   *   in="query",
   *   @OA\Schema(type="integer", format="int64"),
   * ),
   * @OA\Parameter(
   *   parameter="entry_edit_filesize",
   *   name="filesize",
   *   in="query",
   *   @OA\Schema(type="integer", format="int64"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="entry_edit_episodes",
   *   name="episodes",
   *   in="query",
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   * @OA\Parameter(
   *   parameter="entry_edit_ovas",
   *   name="ovas",
   *   in="query",
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   * @OA\Parameter(
   *   parameter="entry_edit_specials",
   *   name="specials",
   *   in="query",
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="entry_edit_season_number",
   *   name="season_number",
   *   in="query",
   *   @OA\Schema(type="integer", format="int32")
   * ),
   * @OA\Parameter(
   *   parameter="entry_edit_season_first_title_id",
   *   name="season_first_title_id",
   *   in="query",
   *   @OA\Schema(type="string", format="uuid")
   * ),
   * @OA\Parameter(
   *   parameter="entry_edit_prequel_id",
   *   name="prequel_id",
   *   in="query",
   *   @OA\Schema(type="string", format="uuid"),
   * ),
   * @OA\Parameter(
   *   parameter="entry_edit_sequel_id",
   *   name="sequel_id",
   *   in="query",
   *   @OA\Schema(type="string", format="uuid"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="entry_edit_encoder_video",
   *   name="encoder_video",
   *   in="query",
   *   @OA\Schema(type="string"),
   * ),
   * @OA\Parameter(
   *   parameter="entry_edit_encoder_audio",
   *   name="encoder_audio",
   *   in="query",
   *   @OA\Schema(type="string"),
   * ),
   * @OA\Parameter(
   *   parameter="entry_edit_encoder_subs",
   *   name="encoder_subs",
   *   in="query",
   *   @OA\Schema(type="string"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="entry_edit_release_year",
   *   name="release_year",
   *   in="query",
   *   @OA\Schema(ref="#/components/schemas/YearSchema"),
   * ),
   * @OA\Parameter(
   *   parameter="entry_edit_release_season",
   *   name="release_season",
   *   in="query",
   *   @OA\Schema(type="string", enum={"Winter", "Spring", "Summer", "Fall"}),
   * ),
   *
   * @OA\Parameter(
   *   parameter="entry_edit_variants",
   *   name="variants",
   *   in="query",
   *   @OA\Schema(type="string"),
   * ),
   * @OA\Parameter(
   *   parameter="entry_edit_remarks",
   *   name="remarks",
   *   in="query",
   *   @OA\Schema(type="string"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="entry_edit_id_codec_audio",
   *   name="id_codec_audio",
   *   in="query",
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   * @OA\Parameter(
   *   parameter="entry_edit_id_codec_video",
   *   name="id_codec_video",
   *   in="query",
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   * @OA\Parameter(
   *   parameter="entry_edit_codec_hdr",
   *   name="codec_hdr",
   *   in="query",
   *   @OA\Schema(type="boolean"),
   * ),
   */
  public function rules() {
    return [
      'id_quality' => 'integer|exists:qualities,id',
      'title' => 'string|max:256',

      'date_finished' => 'string|date|before_or_equal:today',
      'duration' => 'integer|min:0|max:' . db_int_max('medium'),
      'filesize' => 'integer|min:0|max:' . db_int_max('bigint'),

      'episodes' => 'integer|min:0|max:' . db_int_max('small'),
      'ovas' => 'integer|min:0|max:' . db_int_max('small'),
      'specials' => 'integer|min:0|max:' . db_int_max('small'),

      'season_number' => 'integer|min:0|max:' . db_int_max('tiny'),
      'season_first_title_id' => 'uuid|exists:entries,uuid',
      'prequel_id' => 'uuid|exists:entries,uuid',
      'sequel_id' => 'uuid|exists:entries,uuid',

      'encoder_video' => 'string|max:128',
      'encoder_audio' => 'string|max:128',
      'encoder_subs' => 'string|max:128',

      'release_year' => year_validation(),
      'release_season' => [new Enum(SeasonsEnum::class)],

      'variants' => 'string|max:256',
      'remarks' => 'string|max:256',

      'id_codec_audio' => 'integer|exists:codec_audios,id',
      'id_codec_video' => 'integer|exists:codec_videos,id',
      'codec_hdr' => 'boolean',
    ];
  }

  public function failedValidation(Validator $validator) {
    throw new HttpResponseException(
      response()->json([
        'status' => 401,
        'data' => $validator->errors(),
      ], 401)
    );
  }

  public function messages() {
    $validation = require config_path('validation.php');

    return array_merge($validation, []);
  }
}
