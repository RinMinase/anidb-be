<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('pc_setups', function (Blueprint $table) {
      $table->id();

      $table->integer('id_owner')->nullable();
      $table->foreign('id_owner')->references('id')->on('pc_owners')->onDelete('cascade');

      $table->integer('id_info')->nullable();
      $table->foreign('id_info')->references('id')->on('pc_infos')->onDelete('cascade');

      $table->integer('id_component')->nullable();
      $table->foreign('id_component')->references('id')->on('pc_components')->onDelete('cascade');

      $table->smallInteger('count')->default(1);

      $table->timestamps();
      $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('pc_setups');
  }
};
