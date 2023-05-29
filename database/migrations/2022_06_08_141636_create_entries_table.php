<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('entries', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();

      $table->integer('id_quality')->nullable();
      $table->foreign('id_quality')->references('id')->on('qualities');

      $table->string('title', 256)->nullable();
      $table->date('date_finished')->nullable();
      $table->mediumInteger('duration')->nullable();
      $table->bigInteger('filesize')->nullable();

      $table->smallInteger('episodes')->default(0)->nullable();
      $table->smallInteger('ovas')->default(0)->nullable();
      $table->smallInteger('specials')->default(0)->nullable();

      $table->tinyInteger('season_number')->nullable();
      $table->integer('season_first_title_id')->nullable();
      $table->foreign('season_first_title_id')->references('id')->on('entries');

      $table->integer('prequel_id')->nullable();
      $table->foreign('prequel_id')->references('id')->on('entries');
      $table->integer('sequel_id')->nullable();
      $table->foreign('sequel_id')->references('id')->on('entries');

      $table->string('encoder_video', 128)->nullable();
      $table->string('encoder_audio', 128)->nullable();
      $table->string('encoder_subs', 128)->nullable();

      $table->boolean('codec_hdr')->nullable();
      $table->integer('id_codec_video')->nullable();
      $table->foreign('id_codec_video')->references('id')->on('codec_videos');
      $table->integer('id_codec_audio')->nullable();
      $table->foreign('id_codec_audio')->references('id')->on('codec_audios');

      $table->smallInteger('release_year')->nullable();
      $table->enum(
        'release_season',
        ['Winter', 'Spring', 'Summer', 'Fall'],
      )->nullable();

      $table->string('variants', 256)->nullable();
      $table->string('remarks', 256)->nullable();

      $table->timestamps();
      $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('entries');
  }
};
