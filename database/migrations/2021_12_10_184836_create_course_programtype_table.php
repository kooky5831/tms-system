<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseProgramtypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_main_program_type', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('course_main_id')->unsigned()->index();
            $table->foreign('course_main_id')->references('id')->on('course_mains')->onUpdate('cascade')->onDelete('cascade');
            $table->bigInteger('program_type_id')->unsigned()->index();
            $table->foreign('program_type_id')->references('id')->on('program_types')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_program_type');
    }
}
