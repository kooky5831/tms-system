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
        Schema::table('assessment_submission_attachments', function (Blueprint $table) {
            $table->dropForeign(['mainexam_id']);
            $table->dropColumn(['mainexam_id']);

            $table->unsignedBigInteger('assessment_id')->after('id')->index();
            $table->foreign('assessment_id')->references('id')->on('tms_exam_assessments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessment_submission_attachments', function (Blueprint $table) {
            $table->unsignedBigInteger('mainexam_id')->index();
            $table->foreign('mainexam_id')->references('id')->on('assessment_main_exams')->onDelete('cascade');
        });
    }
};
