<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursemainTrainerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coursemain_trainer', function (Blueprint $table) {
            $table->unsignedBigInteger('coursemain_id')->index();
            $table->foreign('coursemain_id')->references('id')->on('course_mains')->onDelete('cascade');
            $table->unsignedBigInteger('trainer_id')->index();
            $table->foreign('trainer_id')->references('id')->on('trainers')->onDelete('cascade');
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
        Schema::dropIfExists('coursemain_trainer');
    }
}
