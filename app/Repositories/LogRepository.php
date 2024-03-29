<?php

namespace App\Repositories;

use Carbon\Carbon;

use App\Models\Log;

use App\Resources\Log\LogResource;

class LogRepository {

  public function getAll($params) {
    // Ordering Parameters
    $column = $params['column'] ?? 'created_at';
    $order = $params['order'] ?? 'desc';

    // Pagination Parameters
    $limit = isset($params['limit']) ? intval($params['limit']) : 30;
    $page = isset($params['page']) ? intval($params['page']) : 1;
    $skip = ($page > 1) ? ($page * $limit - $limit) : 0;

    $logs = Log::orderBy($column, $order)
      ->orderBy('id', 'asc');

    $total = $logs->count();
    $total_pages = ceil($total / $limit);
    $has_next = $page < $total_pages;

    $logs = $logs->skip($skip)
      ->paginate($limit);

    return [
      'data' => LogResource::collection($logs),
      'meta' => [
        'page' => $page,
        'limit' => intval($limit),
        'results' => count($logs),
        'total_results' => $total,
        'total_pages' => $total_pages,
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
      'table_changed' => $table_changed,
      'id_changed' => $id_changed,
      'desc' => $desc,
      'action' => $action,
    ];

    Log::create($data);

    // Keeps the latest 200 entries in logs
    $last_id = Log::latest()->pluck('id')->first();
    Log::where('id', '<=', $last_id - config('app.logs_to_keep'))
      ->delete();
  }
}
