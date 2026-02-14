<?php

namespace App\Enums;

enum CarGasOrderColumnsEnum: string {
  case DATE = 'date';
  case ODOMETER = 'odometer';
  case PRICE_PER_LITER = 'price_per_liter';
}
