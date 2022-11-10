<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class WideCarouselImage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('front_carousels', function (Blueprint $table) {
          $table->string('wide_image', 255)->nullable();
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('front_carousels', function (Blueprint $table) {
          $table->dropColumn('wide_image');
      });
    }
}