<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->integer('client_id')->unsigned();
            $table->integer('server_id')->unsigned()->nullable();
            $table->decimal('total', 16, 6)->unsigned();
            $table->decimal('credit', 16, 6)->unsigned()->default(0);
            $table->decimal('late_fee', 16, 6)->unsigned()->default(0);
            $table->string('payment_method')->nullable();
            $table->text('payment_link')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->boolean('paid')->default(false);
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
        Schema::dropIfExists('invoices');
    }
}
