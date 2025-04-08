<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

use App\Enums\ExportTypesEnum;

use App\Models\Bucket;
use App\Models\BucketSim;
use App\Models\BucketSimInfo;
use App\Models\Catalog;
use App\Models\CodecAudio;
use App\Models\CodecVideo;
use App\Models\Entry;
use App\Models\EntryGenre;
use App\Models\EntryOffquel;
use App\Models\EntryRating;
use App\Models\EntryRewatch;
use App\Models\EntryWatcher;
use App\Models\Export;
use App\Models\Genre;
use App\Models\Group;
use App\Models\Log;
use App\Models\Partial;
use App\Models\PCComponent;
use App\Models\PCComponentType;
use App\Models\PCInfo;
use App\Models\PCOwner;
use App\Models\PCSetup;
use App\Models\Priority;
use App\Models\Quality;
use App\Models\Sequence;

use App\Fourleaf\Models\BillsElectricity;
use App\Fourleaf\Models\Electricity;
use App\Fourleaf\Models\Gas;
use App\Fourleaf\Models\Maintenance;
use App\Fourleaf\Models\MaintenancePart;
use App\Fourleaf\Models\MaintenanceType;

class ProcessExports implements ShouldQueue {

  use Dispatchable, Queueable;

  public function __construct() {
  }

  public function handle(): void {
    // Cleanup unwanted backups
    $this->cleanup_backups();

    // Process unfinished exports
    $unfinished_exports = Export::where('is_finished', false)->orderBy('created_at')->get();

    foreach ($unfinished_exports as $value) {
      $id = $value->id;
      $type = $value->type;

      $contents = '';
      if ($type === ExportTypesEnum::SQL) {
        $contents = $this->process_sql();
      } elseif ($type === ExportTypesEnum::XLSX) {
        $contents = $this->process_xlsx();
      } else {
        // defaults to JSON
        $contents = $this->process_json();
      }

      // generate file
      $file = fopen(storage_path("app/db-dumps/{$id}.{$type}"), 'w');
      fwrite($file, $contents);
      fclose($file);

      // change status to finished
      $value->is_finished = true;
      $value->save();
    }
  }

  private function cleanup_backups() {
    $wherein_subquery = Export::select('id', 'created_at')
      ->addSelect(DB::raw('ROW_NUMBER() OVER (ORDER BY created_at DESC) AS row_num'));

    $for_deletion = Export::whereIn('id', function ($query) use ($wherein_subquery) {
      $query->select('id')
        ->from($wherein_subquery, 'derived_table')
        ->where(function ($query) {
          // Delete expired exports
          $expiry_date = Carbon::now()
            ->subDays(intval(config('app.backups_max_days')))
            ->format('Y-m-d');

          $query->where('created_at', '<=', $expiry_date)
            ->orWhere('row_num', '>', config('app.backups_to_keep'));
        });
    });

    $items = $for_deletion->get(['id', 'type'])->toArray();

    // Remove from actual storage
    foreach ($items as $item) {
      $filename = "{$item['id']}.{$item['type']}";

      if (file_exists(storage_path('app/db-dumps/' . $filename))) {
        unlink(storage_path('app/db-dumps/' . $filename));
      }
    }

    // Remove from database
    $for_deletion->delete();
  }

  private function process_sql(): string {
    return '';
  }

  private function process_xlsx(): string {
    return '';
  }

  public function process_json(): string {
    // Buckets
    $hidden_columns = ['id', 'created_at', 'updated_at'];
    $bucket_sim_infos = BucketSimInfo::all()->makeVisible($hidden_columns)->toArray();

    $hidden_columns = ['id', 'created_at', 'updated_at'];
    $bucket_sims = BucketSim::all()->makeVisible($hidden_columns)->toArray();

    $buckets = Bucket::all()->toArray();

    // Catalogs, Partials & Sequences
    $hidden_columns = ['id', 'updated_at', 'deleted_at'];
    $catalogs = Catalog::all()->makeVisible($hidden_columns)->toArray();

    $hidden_columns = ['id', 'created_at', 'updated_at', 'deleted_at'];
    $partials = Partial::all()->makeVisible($hidden_columns)->toArray();

    $hidden_columns = ['created_at', 'updated_at'];
    $sequences = Sequence::all()->makeVisible($hidden_columns)->toArray();

    // Entries
    $entries_watchers = EntryWatcher::all()->toArray();

    $hidden_columns = ['id_entries'];
    $entries_genres = EntryGenre::all()->makeVisible($hidden_columns)->toArray();

    $hidden_columns = ['id', 'id_entries'];
    $entries_rewatch = EntryRewatch::all()->makeVisible($hidden_columns)->toArray();

    $hidden_columns = ['id', 'id_entries', 'created_at', 'updated_at', 'deleted_at'];
    $entries_rating = EntryRating::all()->makeVisible($hidden_columns)->toArray();

    $hidden_columns = ['id_entries', 'created_at', 'updated_at', 'deleted_at'];
    $entries_offquel = EntryOffquel::all()->makeVisible($hidden_columns)->toArray();

    $hidden_columns = ['id', 'id_quality', 'updated_at', 'deleted_at'];
    $entries = Entry::all()->makeVisible($hidden_columns)->toArray();

    // Codecs
    $hidden_columns = ['created_at', 'updated_at'];
    $codecs_audio = CodecAudio::all()->makeVisible($hidden_columns)->toArray();

    $hidden_columns = ['created_at', 'updated_at'];
    $codecs_video = CodecVideo::all()->makeVisible($hidden_columns)->toArray();

    // Other Dropdowns
    $genres = Genre::all()->toArray();
    $priorities = Priority::all()->toArray();
    $qualities = Quality::all()->toArray();

    $hidden_columns = ['id', 'created_at', 'updated_at'];
    $groups = Group::all()->makeVisible($hidden_columns)->toArray();

    // Logs
    $hidden_columns = ['id'];
    $logs = Log::all()->makeVisible($hidden_columns)->toArray();

    // PC
    $pc_component_types = PCComponentType::all()->toArray();
    $pc_components = PCComponent::all()->toArray();

    $hidden_columns = ['id'];
    $pc_owners = PCOwner::all()->makeVisible($hidden_columns)->toArray();

    $hidden_columns = ['id'];
    $pc_infos = PCInfo::all()->makeVisible($hidden_columns)->toArray();

    $pc_setups = PCSetup::all()->toArray();

    // Fourleaf
    $hidden_columns = ['id'];
    $fourleaf_bills_electricity = BillsElectricity::all()->makeVisible($hidden_columns)->toArray();

    $fourleaf_electricity = Electricity::all()->toArray();
    $fourleaf_gas = Gas::all()->toArray();
    $fourleaf_maintenance = Maintenance::all()->toArray();
    $fourleaf_maintenance_parts = MaintenancePart::all()->toArray();
    $fourleaf_maintenance_types = MaintenanceType::all()->toArray();


    $data = [
      'bucket_sim_infos' => $bucket_sim_infos,
      'bucket_sims' => $bucket_sims,
      'buckets' => $buckets,

      'catalogs' => $catalogs,
      'partials' => $partials,
      'sequences' => $sequences,

      'entries_watchers' => $entries_watchers,
      'entries_genres' => $entries_genres,
      'entries_rewatch' => $entries_rewatch,
      'entries_rating' => $entries_rating,
      'entries_offquel' => $entries_offquel,
      'entries' => $entries,

      'codecs_audio' => $codecs_audio,
      'codecs_video' => $codecs_video,

      'genres' => $genres,
      'priorities' => $priorities,
      'qualities' => $qualities,
      'groups' => $groups,

      'logs' => $logs,

      'pc_component_types' => $pc_component_types,
      'pc_components' => $pc_components,
      'pc_owners' => $pc_owners,
      'pc_infos' => $pc_infos,
      'pc_setups' => $pc_setups,

      'fourleaf_bills_electricity' => $fourleaf_bills_electricity,
      'fourleaf_electricity' => $fourleaf_electricity,
      'fourleaf_gas' => $fourleaf_gas,
      'fourleaf_maintenance' => $fourleaf_maintenance,
      'fourleaf_maintenance_parts' => $fourleaf_maintenance_parts,
      'fourleaf_maintenance_types' => $fourleaf_maintenance_types,
    ];

    return json_encode($data, JSON_PRETTY_PRINT);
  }
}
