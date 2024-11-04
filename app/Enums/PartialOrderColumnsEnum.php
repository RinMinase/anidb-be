<?php

namespace App\Enums;

enum PartialOrderColumnsEnum: string {
  case CATALOG = 'id_catalog'; // default
  case PRIORITY = 'id_priority';
  case TITLE = 'title';
}
