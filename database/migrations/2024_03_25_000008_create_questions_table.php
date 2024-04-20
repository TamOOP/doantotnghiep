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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_id');
            $table->longText('content');
            $table->boolean('multi_answer');
            $table->float('mark', 8, 2);
            $table->enum('choice_numbering', ['abc', 'ABCD', 'iii', 'IIII', 'none']);
            $table->enum('status', [1, 0, -1])->default(1);

            $table->foreign('exam_id')->references('id')->on('examinations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('questions');
    }
};
