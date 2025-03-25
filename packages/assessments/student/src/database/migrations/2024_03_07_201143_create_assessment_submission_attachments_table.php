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
        Schema::create('assessment_submission_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mainexam_id')->index();
            $table->foreign('mainexam_id')->references('id')->on('assessment_main_exams')->onDelete('cascade');
            $table->unsignedBigInteger('question_id')->index();
            $table->foreign('question_id')->references('id')->on('assessment_questions')->onDelete('cascade');
            $table->unsignedBigInteger('student_enrol_id')->index();
            $table->foreign('student_enrol_id')->references('id')->on('student_enrolments')->onDelete('cascade');
            $table->string('submission_attchment');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_submission_attachments');
    }
};
