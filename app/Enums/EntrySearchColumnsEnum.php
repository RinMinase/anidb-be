<?php

namespace App\Enums;

enum EntrySearchColumnsEnum: string {
  case QUALITY = 'quality';
  case TITLE = 'title';
  case DATE_FINISHED = 'date';
  case FILESIZE = 'size';
  case EPISODES = 'episodes';
  case OVAS = 'ovas';
  case SPECIALS = 'specials';
  case ENCODER = 'encoder';
  case RELEASE = 'release';
  case REMARKS = 'remarks';
  case VARIANTS = 'variants';
}
