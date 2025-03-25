<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseRunTriggersCourseMainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_run_triggers_course_mains', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_run_trigger_id');
            $table->foreign('course_run_trigger_id')->references('id')->on('course_run_triggers')->onDelete('cascade');
            $table->unsignedBigInteger('course_mains_id');
            $table->foreign('course_mains_id')->references('id')->on('course_mains')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_run_triggers_course_mains');
    }
}
