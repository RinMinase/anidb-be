<?php

namespace App\Repositories;

use Illuminate\Support\Str;

use App\Models\User;

class UserRepository {

  public function getAll() {
    return User::select()->where('is_admin', false)->get();
  }

  public function get($uuid) {
    return User::select()->where('uuid', $uuid)
      ->firstOrFail();
  }

  public function add(array $values, $is_admin = false) {
    $uid = Str::uuid()->toString();
    $user = User::create([
      'uuid' => $uid,
      'username' => $values['username'],
      'password' => bcrypt($values['password']),
      'is_admin' => $is_admin,
    ]);

    $log_data = [
      'username' => $values['username'],
      'is_admin' => $is_admin,
    ];

    LogRepository::generateLogs('users', $uid, $log_data, 'add');

    return $user;
  }

  public function edit(array $values, $uuid) {
    $user = User::where('uuid', $uuid)
      ->firstOrFail()
      ->update([
        'username' => $values['username'],
        'password' => bcrypt($values['password']),
      ]);

    $log_data = [
      'username' => $values['username'],
    ];

    LogRepository::generateLogs('users', $uuid, $log_data, 'edit');

    return $user;
  }

  public function delete($uuid) {
    $user = User::where('uuid', $uuid)
      ->where('is_admin', false)
      ->firstOrFail()
      ->delete();

    LogRepository::generateLogs('users', $uuid, null, 'delete');

    return $user;
  }
}
