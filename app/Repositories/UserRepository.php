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
    return User::create([
      'uuid' => Str::uuid()->toString(),
      'username' => $values['username'],
      'password' => bcrypt($values['password']),
      'is_admin' => $is_admin,
    ]);
  }

  public function edit(array $values, $uuid) {
    return User::where('uuid', $uuid)
      ->firstOrFail()
      ->update([
        'username' => $values['username'],
        'password' => bcrypt($values['password']),
      ]);
  }

  public function delete($uuid) {
    return User::where('uuid', $uuid)
      ->firstOrFail()
      ->delete();
  }
}
