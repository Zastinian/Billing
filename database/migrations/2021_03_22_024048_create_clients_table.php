<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->integer('user_id')->unsigned()->nullable()->unique();
            $table->string('password')->unique();
            $table->integer('referer_id')->unsigned()->nullable();
            $table->decimal('credit', 16, 6)->unsigned()->default(0);
            $table->integer('clicks')->unsigned()->default(0);
            $table->integer('sign_ups')->unsigned()->default(0);
            $table->integer('purchases')->unsigned()->default(0);
            $table->decimal('commissions', 16, 6)->unsigned()->default(0);
            $table->string('currency')->default(0.000000);
            $table->string('country')->default('Global');
            $table->string('timezone')->default('UTC');
            $table->string('language')->default('EN');
            $table->boolean('auto_renew')->default(true);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_admin')->default(false);
            $table->rememberToken();
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
        Schema::dropIfExists('clients');
    }
}
