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
        Schema::table('course_run_triggers', function (Blueprint $table) {
            $table->dropColumn(['course_main_id', 'group_no']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_run_triggers', function (Blueprint $table) {
            $table->unsignedBigInteger('course_main_id')->index();
            $table->foreign('course_main_id')->references('id')->on('course_mains')->onDelete('cascade');
            $table->integer('group_no')->nullable();
        });
    }
};
