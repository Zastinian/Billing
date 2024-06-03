<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddonCyclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addon_cycles', function (Blueprint $table) {
            $table->id();
            $table->integer('addon_id')->unsigned();
            $table->integer('cycle_length')->unsigned();
            $table->tinyInteger('cycle_type')->unsigned();
            $table->decimal('init_price', 16, 6)->unsigned();
            $table->decimal('renew_price', 16, 6)->unsigned();
            $table->decimal('setup_fee', 16, 6)->unsigned()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addon_cycles');
    }
}
