<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->integer('server_id')->unsigned()->nullable()->unique();
            $table->string('identifier')->nullable()->unique();
            $table->integer('client_id')->unsigned();
            $table->integer('plan_id')->unsigned();
            $table->integer('plan_cycle')->unsigned();
            $table->timestamp('due_date')->nullable();
            $table->string('payment_method');
            $table->string('server_name');
            $table->integer('nest_id')->unsigned();
            $table->integer('egg_id')->unsigned();
            $table->integer('location_id')->unsigned();
            $table->integer('node_id')->unsigned();
            $table->string('ip_address')->nullable();
            $table->tinyInteger('status')->unsigned()->default(1);
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
        Schema::dropIfExists('servers');
    }
}
