<?php

namespace App\Enums;

enum CarEfficiencyGraphTypeEnum: string {
  case LAST20DATA = 'last20data';
  case LAST12MOS = 'last12mos';
}
