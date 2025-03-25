<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseMainsTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_mains_tags', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_mains_id');
            $table->foreign('course_mains_id')->references('id')->on('course_mains')->onDelete('cascade');
            $table->unsignedBigInteger('course_tag_id');
            $table->foreign('course_tag_id')->references('id')->on('course_tags')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_mains_tags');
    }
}
