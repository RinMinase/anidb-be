<?php

namespace App\Fourleaf\Enums;

enum GetGasOrderColumnsEnum: string {
  case DATE = 'date';
  case ODOMETER = 'odometer';
  case PRICE_PER_LITER = 'price_per_liter';
}
