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
        //
        Schema::table('assessment_main_course_exams', function (Blueprint $table) {
            $table->dropForeign(['mainexam_id']);
            $table->dropColumn('mainexam_id');
            $table->dropForeign(['coursemain_id']);
            $table->dropColumn('coursemain_id');
            $table->unsignedBigInteger('exam_id')->after('id')->nullable();
            $table->foreign('exam_id')->references('id')->on('tms_exams')->onDelete('cascade');
            $table->unsignedBigInteger('course_main_id')->after('exam_id')->nullable();
            $table->foreign('course_main_id')->references('id')->on('course_mains')->onDelete('cascade');
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            
        });

        Schema::rename('assessment_main_course_exams', 'tms_exam_course_mains');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('tms_exam_course_mains', function (Blueprint $table) {
            $table->unsignedBigInteger('mainexam_id')->nullable();
            $table->foreign('exam_id')->references('id')->on('tms_exams')->onDelete('cascade');
            $table->unsignedBigInteger('coursemain_id')->nullable();
            $table->foreign('coursemain_id')->references('id')->on('course_mains')->onDelete('cascade');
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
        });
        Schema::rename('tms_exam_course_mains', 'assessment_main_course_exams');
    }
};
