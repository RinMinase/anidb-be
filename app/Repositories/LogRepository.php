<?php

namespace App\Repositories;

use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\Log;
use App\Resources\Log\LogCollection;

class LogRepository {

  public function getAll($params) {
    $column = $params['column'] ?? 'created_at';
    $order = $params['order'] ?? 'desc';
    $limit = $params['limit'] ?? 30;
    $page = $params['page'] ?? 1;
    $skip = ($page > 1) ? ($page * $limit - $limit) : 0;

    $total = Log::count();
    $total_pages = ceil($total / $limit);
    $has_next = intval($page) < $total_pages;

    $logs = Log::orderBy($column, $order)
      ->orderBy('id', 'asc')
      ->skip($skip)
      ->paginate($limit);

    return [
      'data' => LogCollection::collection($logs),
      'meta' => [
        'page' => $page,
        'limit' => $limit,
        'total' => $total_pages,
        'has_next' => $has_next,
      ],
    ];
  }

  public static function generateLogs(
    $table_changed = null,
    $id_changed = null,
    $desc = null,
    $action = null,
  ) {
    $data = [
      'uuid' => Str::uuid()->toString(),
      'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
      'table_changed' => $table_changed,
      'id_changed' => $id_changed,
      'desc' => $desc,
      'action' => $action,
    ];

    Log::create($data);

    // Keeps the latest 200 entries in logs
    $last_id = Log::latest()->pluck('id')->first();
    Log::where('id', '<=', $last_id - env('LOGS_TO_KEEP', 200))
      ->delete();
  }
}
