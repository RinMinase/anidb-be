<?php

namespace App\Fourleaf\Enums;

enum EfficiencyGraphTypeEnum: string {
  case ALL = 'all';
  case LAST5 = 'last5';
  case LAST10 = 'last10';
}
