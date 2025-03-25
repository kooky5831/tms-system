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
        Schema::table('assessment_exam_courses', function (Blueprint $table) {
            $table->dropIndex('assessment_exam_courses_deleted_at_index');
            $table->unsignedBigInteger('exam_id')->change();
            $table->renameColumn('exam_id', 'assessment_id');
            $table->unsignedBigInteger('course_id')->change();
            $table->renameColumn('course_id', 'course_run_id');
            // $table->foreign('assessment_id')->references('id')->on('exam_assessments')->onDelete('cascade');
            // $table->foreign('course_run_id')->references('id')->on('courses')->onDelete('cascade');
        });

        Schema::rename('assessment_exam_courses', 'tms_exam_assement_course_runs');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('tms_exam_assement_course_runs', function (Blueprint $table) {
            // $table->dropForeign('assessment_id');
            // $table->dropForeign('course_run_id');
            $table->renameColumn('assessment_id', 'exam_id');
            $table->renameColumn('course_run_id', 'course_id');
        });

        Schema::rename('tms_exam_assement_course_runs', 'assessment_exam_courses');

        Schema::table('assessment_exam_courses', function (Blueprint $table) {
            $table->index(['deleted_at']);
        });
    }
};
