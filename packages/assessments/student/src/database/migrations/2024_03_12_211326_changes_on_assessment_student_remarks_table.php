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
        Schema::table('assessment_student_remarks', function (Blueprint $table) {
            $table->dropIndex('assessment_student_remarks_deleted_at_index');
            $table->unsignedBigInteger('exam_id')->change();
            $table->renameColumn('exam_id', 'assessment_id');
            $table->bigInteger('created_by');
            $table->bigInteger('updated_by');
            // $table->foreign('assessment_id')->references('id')->on('tms_exam_assessments')->onDelete('cascade');
        });
        Schema::rename('assessment_student_remarks', 'tms_student_assessment_remarks');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('tms_student_assessment_remarks', function (Blueprint $table) {
            $table->tinyInteger('assessment_id')->change();
            $table->renameColumn('assessment_id', 'exam_id');
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
        });
        
        Schema::rename('tms_student_assessment_remarks', 'assessment_student_remarks');
        
        Schema::table('assessment_student_remarks', function (Blueprint $table) {
            $table->index(['deleted_at']);
        });
    }
};
