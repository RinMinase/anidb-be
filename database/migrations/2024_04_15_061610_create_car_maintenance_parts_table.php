<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('car_maintenance_parts', function (Blueprint $table) {
      $table->id();

      $table->integer('id_car_maintenance')->unsigned();
      $table->foreign('id_car_maintenance')->references('id')->on('car_maintenance')->onDelete('cascade');

      $table->integer('id_car_maintenance_type')->unsigned();
      $table->foreign('id_car_maintenance_type')->references('id')->on('car_maintenance_types');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('car_maintenance_parts');
  }
};
