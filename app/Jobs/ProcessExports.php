<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\DbDumper\Databases\PostgreSql as PostgreSqlDumper;

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

use App\Fourleaf\Models\BillsElectricity as FourleafBillsElectricity;
use App\Fourleaf\Models\Electricity as FourleafElectricity;
use App\Fourleaf\Models\Gas as FourleafGas;
use App\Fourleaf\Models\Maintenance as FourleafMaintenance;
use App\Fourleaf\Models\MaintenancePart as FourleafMaintenancePart;
use App\Fourleaf\Models\MaintenanceType as FourleafMaintenanceType;
use App\Fourleaf\Models\Settings as FourleafSettings;

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
      $type = ExportTypesEnum::tryFrom($value->type);

      if ($type === ExportTypesEnum::SQL) {
        if (config('app.platform') === 'production') {
          $this->process_heroku_sql($id);
        } else {
          $this->process_sql($id);
        }
      } elseif ($type === ExportTypesEnum::XLSX) {
        $this->process_xlsx($id);
      } else {
        $this->process_json($id);
      }

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

  private function process_sql(string $uuid) {
    $tables = [
      'bucket_sim_infos',
      'bucket_sims',
      'buckets',
      'catalogs',
      'codecs_audio',
      'codecs_video',
      'entries',
      'entries_genre',
      'entries_offquel',
      'entries_rating',
      'entries_rewatch',
      'entries_watchers',
      'exports',
      'fourleaf_bills_electricity',
      'fourleaf_electricity',
      'fourleaf_gas',
      'fourleaf_maintenance',
      'fourleaf_maintenance_parts',
      'fourleaf_maintenance_types',
      'fourleaf_settings',
      'genres',
      'groups',
      'logs',
      'partials',
      'pc_component_types',
      'pc_components',
      'pc_infos',
      'pc_owners',
      'pc_setups',
      'priorities',
      'qualities',
      'sequences',
    ];

    $connection_url = env('DATABASE_URL') ?? config('database.connections.pgsql.url');

    if (!$connection_url) {
      $config = config('database.connections.pgsql');

      $connection_url = 'postgresql://';
      $connection_url .= $config['username'] . ':';
      $connection_url .= $config['password'] . '@';
      $connection_url .= $config['host'] . ':';
      $connection_url .= $config['port'] . '/';
      $connection_url .= $config['database'];
    }

    PostgreSqlDumper::create()
      ->setDatabaseUrl($connection_url)
      ->includeTables($tables)
      ->dumpToFile(Storage::disk('local')->path('db-dumps/'. $uuid . '.sql'));
  }

  private function process_heroku_sql(string $uuid) {
    Storage::disk('local')->put("db-dumps/{$uuid}.sql", '');
  }

  private function process_xlsx(string $uuid) {
    Storage::disk('local')->put("db-dumps/{$uuid}.xlsx", '');
  }

  private function process_json(string $uuid) {
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
    $entries_genre = EntryGenre::all()->makeVisible($hidden_columns)->toArray();

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
    $fourleaf_bills_electricity = FourleafBillsElectricity::all()->makeVisible($hidden_columns)->toArray();

    $fourleaf_electricity = FourleafElectricity::all()->toArray();
    $fourleaf_gas = FourleafGas::all()->toArray();
    $fourleaf_maintenance = FourleafMaintenance::all()->toArray();
    $fourleaf_maintenance_parts = FourleafMaintenancePart::all()->toArray();
    $fourleaf_maintenance_types = FourleafMaintenanceType::all()->toArray();
    $fourleaf_settings = FourleafSettings::all()->toArray();

    $data = [
      'bucket_sim_infos' => $bucket_sim_infos,
      'bucket_sims' => $bucket_sims,
      'buckets' => $buckets,

      'catalogs' => $catalogs,
      'partials' => $partials,
      'sequences' => $sequences,

      'entries_watchers' => $entries_watchers,
      'entries_genre' => $entries_genre,
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
      'fourleaf_settings' => $fourleaf_settings,
    ];

    $contents = json_encode($data, JSON_PRETTY_PRINT);

    Storage::disk('local')->put("db-dumps/{$uuid}.json", $contents);
  }
}
