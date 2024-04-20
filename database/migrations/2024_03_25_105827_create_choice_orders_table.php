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
        Schema::create('choice_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('qorder_id');
            $table->unsignedBigInteger('choice_id');
            $table->integer('index');
            $table->boolean('selected')->default(0);

            $table->foreign('qorder_id')->references('id')->on('question_orders')->onDelete('cascade');
            $table->foreign('choice_id')->references('id')->on('choices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('choice_orders');
    }
};
