<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration {

  public function up() {
    Schema::create('users', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();

      $table->string('username')->unique();
      $table->string('password');

      $table->timestamps();
    });
  }

  public function down() {
    Schema::dropIfExists('users');
  }
}
