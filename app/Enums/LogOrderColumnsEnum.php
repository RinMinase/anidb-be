<?php

namespace App\Enums;

enum LogOrderColumnsEnum: string {
  case TABLE_CHANGED = 'table_changed';
  case DESCRIPTION = 'description';
  case ACTION = 'action';
  case CRAEATED_AT = 'created_at';
}
