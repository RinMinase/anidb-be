<?php

namespace App\Fourleaf\Resources\Electricity;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *  schema="FourleafElectricityGetResource",
 *
 *   @OA\Property(
 *     property="settings",
 *     @OA\Property(property="kwhPrice", type="float", example=12.23),
 *     @OA\Property(property="monthStartsAt", type="string", example="monday"),
 *   ),
 *
 *   @OA\Property(
 *     property="weekly",
 *     type="array",
 *     @OA\Items(
 *       @OA\Property(property="id", type="string", format="uuid", example="e9597119-8452-4f2b-96d8-f2b1b1d2f158"),
 *       @OA\Property(property="weekNo", type="integer", example=1),
 *       @OA\Property(property="actualWeekYearNo", type="integer", example=12),
 *       @OA\Property(property="totalKwh", type="float", example=12.34),
 *       @OA\Property(property="totalKwhValue", type="integer", example=123),
 *       @OA\Property(property="avgDailyKwh", type="float", example=12.34),
 *     ),
 *   ),
 *
 *   @OA\Property(
 *     property="daily",
 *     type="array",
 *     @OA\Items(
 *       @OA\Property(property="id", type="string", format="uuid", example="e9597119-8452-4f2b-96d8-f2b1b1d2f158"),
 *       @OA\Property(property="dateNumber", type="integer", example=1),
 *       @OA\Property(property="day", type="string", example="monday"),
 *       @OA\Property(property="date", type="string", example="2023-05-21"),
 *       @OA\Property(property="kwPerHour", type="float", example=12.34),
 *       @OA\Property(property="kwUsedToday", type="float", example=12.34),
 *       @OA\Property(property="readingValue", type="float", example=12.34),
 *       @OA\Property(property="readingTime", type="float", example=12.34),
 *       @OA\Property(property="state", type="string", example="low|normal|high"),
 *     ),
 *   ),
 * )
 */
class GetResource extends JsonResource {

  public function toArray($request) {
    return [
      'settings' => convert_arr_to_camel_case($this['settings']),
      'weekly' => convert_arr_to_camel_case($this['weekly']),
      'daily' => convert_arr_to_camel_case($this['daily']),
    ];
  }
}
