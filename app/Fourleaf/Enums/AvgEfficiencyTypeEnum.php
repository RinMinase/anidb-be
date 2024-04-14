<?php

namespace App\Fourleaf\Enums;

enum AvgEfficiencyTypeEnum: string {
  case ALL = 'all';
  case LAST5 = 'last5';
  case LAST10 = 'last10';
}
