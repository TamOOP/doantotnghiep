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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->longText('description')->nullable();
            $table->string('image_path')->nullable();
            $table->dateTime('course_end')->nullable();
            $table->dateTime('course_start')->nullable();
            //0: manual, 1:self, 2: payment
            $table->enum('enrolment_method', [0, 1, 2])->default(0);
            $table->integer('payment_cost')->default(0);
            $table->dateTime('date_modified');
            //1:active, 0: inactive, -1: delete
            $table->enum('status', [1, 0, -1])->default(1);

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('courses');
    }
};
