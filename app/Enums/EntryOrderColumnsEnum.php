<?php

namespace App\Enums;

enum EntryOrderColumnsEnum: string {
  case QUALITY = 'id_quality';
  case TITLE = 'title';
  case DATE_FINISHED = 'date_finished';
  case FILESIZE = 'filesize';
  case EPISODES = 'episodes';
  case OVAS = 'ovas';
  case SPECIALS = 'specials';
  case RELEASE_YEAR = 'release_year';
  case RELEASE_SEASON = 'release_season';
  case REMARKS = 'remarks';
  case TOTAL_REWATCH_COUNT = 'total_rewatch_count';
}
