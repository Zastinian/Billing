<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanCyclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_cycles', function (Blueprint $table) {
            $table->id();
            $table->integer('plan_id')->unsigned();
            $table->integer('cycle_length')->unsigned();
            $table->tinyInteger('cycle_type')->unsigned();
            $table->decimal('init_price', 16, 6)->unsigned();
            $table->decimal('renew_price', 16, 6)->unsigned();
            $table->decimal('setup_fee', 16, 6)->unsigned()->default(0);
            $table->decimal('late_fee', 16, 6)->unsigned()->default(0);
            $table->integer('trial_length')->unsigned()->nullable();
            $table->tinyInteger('trial_type')->unsigned()->nullable();
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
        Schema::dropIfExists('plan_cycles');
    }
}
