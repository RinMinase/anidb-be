<?php

namespace App\Enums;

enum ExportTypesEnum: string {
  case JSON = 'json'; // default
  case SQL = 'sql';
  case XLSX = 'xlsx';
}
