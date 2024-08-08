<?php

namespace App\Enums;

enum IntegerSizesEnum: string {
  case TINY = 'tiny';
  case SMALL = 'small';
  case MEDIUM = 'medium';
  case DEFAULT = 'default';
  case BIG = 'big';
}
