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
        Schema::create('examinations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('activity_id');
            $table->dateTime('time_start');
            $table->dateTime('time_end')->nullable();
            $table->integer('time_limit')->nullable();
            $table->enum('time_unit', [1,60,3600,86400])->nullable();
            $table->float('grade_pass', 8, 2);
            $table->float('grade_scale', 8, 2);
            $table->integer('attempt_allow');
            //0: highest, 1: average, 2: first attempt, 3: last attempt
            $table->enum('grading_method', [0, 1, 2, 3]);
            $table->boolean('random_answer');
            $table->boolean('shuffle_question');
            $table->integer('question_per_page');
            $table->boolean('show_answer');

            $table->foreign('activity_id')->references('id')->on('activities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('examinations');
    }
};
