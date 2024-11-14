<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('pc_setups_inventories', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();

      $table->integer('id_pc_setups_inventory_type');
      $table->foreign('id_pc_setups_inventory_type')->references('id')->on('pc_setups_inventory_types');

      $table->string('name', 128);
      $table->integer('price')->nullable();
      $table->date('purchase_date')->nullable();
      $table->string('purchase_location', 256)->nullable();
      $table->boolean('is_onhand');

      $table->timestamps();
      $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('pc_setups_inventories');
  }
};
