<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

use App\Models\Traits\RefreshableAutoIncrements;

#[OA\Schema(
  properties: [
    new OA\Property(property: "id", type: "integer", example: 1),
    new OA\Property(property: "createdAt", type: "string", format: "date-time", example: "2020-01-01 00:00:00"),
    new OA\Property(property: "updatedAt", type: "string", format: "date-time", example: "2020-01-01 00:00:00"),

    new OA\Property(property: "billDate", type: "string", format: 'date', example: '2001-01-01'),
    new OA\Property(property: "periodFrom", type: "string", format: 'date', example: '2001-01-01'),
    new OA\Property(property: "periodTo", type: "string", format: 'date', example: '2001-01-01'),

    new OA\Property(property: "avgKwh", type: "float", example: 12.34),
    new OA\Property(property: "readingFrom", type: "integer", example: 1),
    new OA\Property(property: "readingTo", type: "integer", example: 1),

    new OA\Property(property: "readingTotal", type: "integer", example: 1),
    new OA\Property(property: "totalCost", type: "float", example: 12.34),

    new OA\Property(property: "priceGeneration", type: "float", example: 12.34),
    new OA\Property(property: "priceDistribution", type: "float", example: 12.34),
    new OA\Property(property: "priceGovt", type: "float", example: 12.34),

    new OA\Property(property: "kwhPriceGenerationCharge", type: "float", example: 12.34),
    new OA\Property(property: "kwhPriceDistributionCharge", type: "float", example: 12.34),

    new OA\Property(property: "notes", type: "string", example: 'notes'),
  ]
)]
class ElectricityBill extends Model {

  use RefreshableAutoIncrements;

  protected $table = 'electricity_bills';

  protected $fillable = [
    'bill_date',

    'period_from',
    'period_to',

    'calculated_kwh_cost',
    'avg_kwh',
    'reading_from',
    'reading_to',

    'reading_total',
    'total_cost',

    'price_generation',
    'price_distribution',
    'price_govt',

    'kwh_price_generation_charge',
    'kwh_price_transmission_charge',
    'kwh_price_distribution_charge',

    'notes',
  ];

  protected $hidden = [];

  protected function casts(): array {
    return [
      'bill_date' => 'date:Y-m',

      'period_from' => 'date:Y-m-d',
      'period_to' => 'date:Y-m-d',

      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
  }
}
