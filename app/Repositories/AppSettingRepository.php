<?php

namespace App\Repositories;

use App\Models\AppSetting;

class AppSettingRepository {

  public function getAll() {
    return AppSetting::all()->toArray();
  }

  public function get($id) {
    return AppSetting::where('id', $id)->firstOrFail()->toArray();
  }

  public function add(array $values) {
    return AppSetting::create($values);
  }

  public function edit(array $values, $id) {
    return AppSetting::where('id', $id)->update($values);
  }

  public function editByKey(string $key, string $value) {
    return AppSetting::where('key', $key)->update(['value' => $value]);
  }

  public function delete($id) {
    return AppSetting::where('id', $id)->firstOrFail()->delete();
  }
}
