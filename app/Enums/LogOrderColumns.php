<?php

namespace App\Enums;

enum LogOrderColumns: string {
  case TABLE_CHANGED = 'table_changed';
  case DESCRIPTION = 'description';
  case ACTION = 'action';
  case CRAEATED_AT = 'created_at';
}
