<?php

namespace App\Repositories;

use Illuminate\Support\Str;

use App\Models\Rss;
use App\Models\RssItem;

class RssRepository {

  public function getAll() {
    $rss = Rss::select()
      ->orderBy('created_at')
      ->get();

    $output = [];

    foreach ($rss as $rssEntry) {
      $unreadItems = RssItem::where('id_rss', $rssEntry->id)
        ->where('is_read', false)
        ->count();

      $output[] = (object) array_merge(
        $rssEntry->toArray(),
        ['unread' => $unreadItems]
      );
    }

    return collect($output);
  }

  public function get($uuid) {
    $rss = Rss::where('uuid', $uuid)->firstOrFail();

    return RssItem::where('id_rss', $rss->id)
      ->orderBy('date', 'desc')
      ->orderBy('title')
      ->get();
  }

  public function add(array $values) {
    $values['uuid'] = Str::uuid()->toString();

    return Rss::create($values);
  }

  public function edit(array $values, $uuid) {
    return Rss::where('uuid', $uuid)
      ->firstOrFail()
      ->update($values);
  }

  public function delete($uuid) {
    return Rss::where('uuid', $uuid)
      ->firstOrFail()
      ->delete();
  }

  public function toggle_read($uuid) {
    $data = RssItem::where('uuid', $uuid)->firstOrFail();
    $value = $data->is_read;

    $data->update(['is_read' => !$value]);
    $data->save();
  }

  public function toggle_bookmark($uuid) {
    $data = RssItem::where('uuid', $uuid)->firstOrFail();
    $value = $data->is_bookmarked;

    $data->update(['is_bookmarked' => !$value]);
    $data->save();
  }
}
