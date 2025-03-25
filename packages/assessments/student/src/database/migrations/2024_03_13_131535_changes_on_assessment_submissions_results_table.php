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
        Schema::table('assessment_submissions_results', function (Blueprint $table) {
            $table->dropIndex('assessment_submissions_results_deleted_at_index');
            $table->unsignedBigInteger('exam_id')->change();
            $table->renameColumn('exam_id', 'assessment_id');
            $table->bigInteger('created_by');
            $table->bigInteger('updated_by');
            // $table->foreign('question_id')->references('id')->on('tms_questions')->onDelete('cascade');
            // $table->foreign('student_enr_id')->references('id')->on('student_enrolments')->onDelete('cascade');
        });
        Schema::rename('assessment_submissions_results', 'tms_student_results');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('tms_student_results', function (Blueprint $table) {
            $table->renameColumn('assessment_id', 'exam_id');
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
            // $table->dropForeign('question_id');
            // $table->dropForeign('student_enr_id');
        });
        Schema::rename('tms_student_results', 'assessment_submissions_results');
        
        Schema::table('assessment_submissions_results', function (Blueprint $table) {
            $table->index(['deleted_at']);
        });
    }
};
