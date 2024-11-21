<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('pc_components', function (Blueprint $table) {
      $table->id();

      $table->integer('id_type')->nullable();
      $table->foreign('id_type')->references('id')->on('pc_component_types');

      $table->string('name', 128);
      $table->string('description', 128)->nullable();
      $table->string('sub_description', 128)->nullable();

      $table->integer('price')->nullable();
      $table->date('purchase_date')->nullable();
      $table->string('purchase_location', 256)->nullable();

      $table->boolean('is_onhand')->nullable();

      $table->timestamps();
      $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('pc_components');
  }
};
