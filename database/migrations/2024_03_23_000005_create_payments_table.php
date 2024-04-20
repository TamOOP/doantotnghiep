<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->longText('orderId');
            $table->unsignedBigInteger('enrolment_id');
            $table->float('value', 8, 2);
            $table->dateTime('payment_date');
            $table->enum('payment_status', ['process','done']);

            $table->foreign('enrolment_id')->references('id')->on('enrolments');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
