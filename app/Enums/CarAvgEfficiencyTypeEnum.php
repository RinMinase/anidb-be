<?php

namespace App\Enums;

enum CarAvgEfficiencyTypeEnum: string {
  case ALL = 'all';
  case LAST5 = 'last5';
  case LAST10 = 'last10';
}
