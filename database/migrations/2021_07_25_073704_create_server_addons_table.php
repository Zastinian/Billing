<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServerAddonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('server_addons', function (Blueprint $table) {
            $table->id();
            $table->integer('addon_id')->unsigned();
            $table->integer('cycle_id')->unsigned();
            $table->integer('server_id')->unsigned();
            $table->integer('client_id')->unsigned();
            $table->string('value');
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
        Schema::dropIfExists('server_addons');
    }
}
