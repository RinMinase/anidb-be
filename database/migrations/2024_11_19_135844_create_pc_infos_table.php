<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('pc_infos', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();

      $table->integer('id_owner')->nullable();
      $table->foreign('id_owner')->references('id')->on('pc_owners');

      $table->string('label', 128);

      $table->boolean('is_current')->default(false)->nullable();

      $table->timestamps();
      $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('pc_infos');
  }
};
