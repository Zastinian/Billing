<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('category_id')->unsigned();
            $table->integer('ram')->unsigned();
            $table->integer('cpu')->unsigned();
            $table->integer('disk')->unsigned();
            $table->integer('swap');
            $table->integer('io')->unsigned();
            $table->integer('databases')->unsigned();
            $table->integer('backups')->unsigned();
            $table->integer('extra_ports')->unsigned();
            $table->string('locations_nodes_id');
            $table->integer('min_port')->unsigned()->nullable();
            $table->integer('max_port')->unsigned()->nullable();
            $table->string('nests_eggs_id');
            $table->text('server_description')->nullable();
            $table->integer('discount')->unsigned()->nullable();
            $table->string('coupons')->nullable();
            $table->integer('days_before_suspend')->unsigned()->nullable();
            $table->integer('days_before_delete')->unsigned()->nullable();
            $table->integer('global_limit')->unsigned()->nullable();
            $table->integer('per_client_limit')->unsigned()->nullable();
            $table->integer('per_client_trial_limit')->unsigned()->nullable();
            $table->integer('order')->default(1000);
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
        Schema::dropIfExists('plans');
    }
}
