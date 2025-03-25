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
        Schema::create('student_course_attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('session_id');
            $table->foreign('session_id')->references('id')->on('course_sessions')->onDelete('cascade');
            $table->unsignedBigInteger('student_enrolment_id')->index();
            $table->foreign('student_enrolment_id')->references('id')->on('student_enrolments')->onDelete('cascade');
            $table->unsignedBigInteger('course_id');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->tinyInteger('is_present')->default(0)->comment('Present - 1, Absent - 0');
            $table->tinyInteger('attendance_sync')->default(0)->comment('Sync-1, Notsync-0');
            $table->tinyInteger('assessment_sync')->default(0)->comment('Sync-1, Notsync-0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_course_attendances');
    }
};
