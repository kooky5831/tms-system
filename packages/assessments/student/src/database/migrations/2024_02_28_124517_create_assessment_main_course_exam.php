<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assessment_main_course_exams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mainexam_id');
            $table->foreign('mainexam_id')->references('id')->on('assessment_main_exams')->onDelete('cascade');
            $table->unsignedBigInteger('coursemain_id');
            $table->foreign('coursemain_id')->references('id')->on('course_mains')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_main_course_exams');
    }
};
