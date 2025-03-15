<?php

namespace App\Commands;

use App\Repositories\LogRepository;
use Illuminate\Console\Command;

class PruneOldLogData extends Command {

  protected $signature = 'app:prune-old-log-data';
  protected $description = 'Command description';

  public function handle() {
    LogRepository::remove_old_logs();
  }
}
