<?php

namespace App\Requests\Entry;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Enum;

use App\Enums\EntryOrderColumnsEnum;
use App\Enums\EntrySearchHasEnum;

class SearchRequest extends FormRequest {

  /**
   * @OA\Parameter(
   *   parameter="entry_search_quality",
   *   name="quality",
   *   description="Range or Comma separated values<br>Could be common terms: 4K, UHD<br>Vertical pixels: 1080, 1080p<br>Range: 1080p to UHD",
   *   in="query",
   *   example="UHD",
   *   @OA\Schema(type="string"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="entry_search_title",
   *   name="title",
   *   in="query",
   *   example="sample title",
   *   @OA\Schema(type="string"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="entry_search_date",
   *   name="date",
   *   description="Could be a date or date range: '{date}', '{date} to {date}', 'from {date} to {date}';<br>Comparators: '> 2024-10-20', '>= 2024', 'gt 2024-10-20', 'gte 2024', 'greater than 2024-10', 'greater than or equal 2024-10-20'",
   *   in="query",
   *   example="2024-10 to 2024-11",
   *   @OA\Schema(type="string"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="entry_search_filesize",
   *   name="filesize",
   *   description="Could be common terms or byte value: '3 GB', 3000000000<br>Comparators: '> 3 GB'",
   *   in="query",
   *   example="> 3 GB",
   *   @OA\Schema(type="string"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="entry_search_episodes",
   *   name="episodes",
   *   description="Could be absolute value: 3<br>Range: 10 to 12<br>Comparators > 12",
   *   in="query",
   *   example=">= 12",
   *   @OA\Schema(type="string"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="entry_search_ovas",
   *   name="ovas",
   *   description="Could be absolute value: 3<br>Range: 10 to 12<br>Comparators > 12",
   *   in="query",
   *   @OA\Schema(type="string"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="entry_search_specials",
   *   name="specials",
   *   description="Could be absolute value: 3<br>Range: 10 to 12<br>Comparators > 12",
   *   in="query",
   *   @OA\Schema(type="string"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="entry_search_encoder",
   *   name="encoder",
   *   description="This searches / matches for all Video, Audio, Sub Encoders",
   *   in="query",
   *   example="sample encoder",
   *   @OA\Schema(type="string"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="entry_search_encoder_video",
   *   name="encoder_video",
   *   in="query",
   *   @OA\Schema(type="string"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="entry_search_encoder_audio",
   *   name="encoder_audio",
   *   in="query",
   *   @OA\Schema(type="string"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="entry_search_encoder_subs",
   *   name="encoder_subs",
   *   in="query",
   *   @OA\Schema(type="string"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="entry_search_release",
   *   name="release",
   *   description="Could be absolute: spring, 2020, 'spring 2020'<br>Range: '2020 to 2021', 'summer 2020 to spring 2021', '2020 summer to 2022'<br>Comparators: '> spring 2023'",
   *   in="query",
   *   @OA\Schema(type="string"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="entry_search_remarks",
   *   name="remarks",
   *   in="query",
   *   @OA\Schema(type="string"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="entry_search_rating",
   *   name="rating",
   *   description="Max value should be 10<br>Could be absolute value: 3<br>Range: 5 to 10<br>Comparators < 7",
   *   in="query",
   *   example=">= 5",
   *   @OA\Schema(type="string"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="entry_search_has_remarks",
   *   name="has_remarks",
   *   in="query",
   *   example="any",
   *   @OA\Schema(type="string", enum={"any", "yes", "no"}, default="any"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="entry_search_has_image",
   *   name="has_image",
   *   in="query",
   *   example="any",
   *   @OA\Schema(type="string", enum={"any", "yes", "no"}, default="any"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="entry_search_is_hdr",
   *   name="is_hdr",
   *   in="query",
   *   example="any",
   *   @OA\Schema(type="string", enum={"any", "yes", "no"}, default="any"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="entry_search_codec_video",
   *   name="codec_video",
   *   description="Comma separated IDs of video codecs",
   *   in="query",
   *   example="1,2",
   *   @OA\Schema(type="string"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="entry_search_codec_audio",
   *   name="codec_audio",
   *   description="Comma separated IDs of audio codecs",
   *   in="query",
   *   @OA\Schema(type="string"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="entry_search_genres",
   *   name="genres",
   *   description="Comma separated IDs of genres",
   *   in="query",
   *   @OA\Schema(type="string"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="entry_search_rewatches",
   *   name="rewatches",
   *   description="Could be absolute value: 3<br>Range: 10 to 12<br>Comparators > 1",
   *   in="query",
   *   example="> 1",
   *   @OA\Schema(type="string"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="entry_search_watcher",
   *   name="watcher",
   *   description="ID of watcher (null = any, 0 = null in DB)",
   *   in="query",
   *   @OA\Schema(type="integer", format="int32"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="entry_search_column",
   *   name="column",
   *   description="Order - Column to order",
   *   in="query",
   *   example="id_quality",
   *   @OA\Schema(type="string", default="id_quality"),
   * ),
   *
   * @OA\Parameter(
   *   parameter="entry_search_order",
   *   name="order",
   *   description="Order - Direction of order column",
   *   in="query",
   *   @OA\Schema(type="string", default="asc", enum={"asc", "desc"}),
   * ),
   */
  public function rules() {
    return [
      'quality' => ['string'],
      'title' => ['string'],
      'date' => ['string'],
      'filesize' => ['string'],

      'episodes' => ['string'],
      'ovas' => ['string'],
      'specials' => ['string'],

      'encoder' => ['string'],
      'encoder_video' => ['string'],
      'encoder_audio' => ['string'],
      'encoder_subs' => ['string'],

      'release' => ['string'],
      'remarks' => ['string'],
      'rating' => ['string'],

      'has_remarks' => [new Enum(EntrySearchHasEnum::class)],
      'has_image' => [new Enum(EntrySearchHasEnum::class)],

      'is_hdr' => [new Enum(EntrySearchHasEnum::class)],
      'codec_video' => ['string'],
      'codec_audio' => ['string'],

      'genres' => ['string'],
      'rewatches' => ['string'],
      'watcher' => ['integer', 'min:0'],

      'column' => [new Enum(EntryOrderColumnsEnum::class)],
      'order' => ['in:asc,desc,ASC,DESC'],
    ];
  }

  public function failedValidation(Validator $validator) {
    /** @disregard TypeInvalid */
    throw new HttpResponseException(
      response()->json([
        'status' => 401,
        'data' => $validator->errors(),
      ], 401)
    );
  }
}
