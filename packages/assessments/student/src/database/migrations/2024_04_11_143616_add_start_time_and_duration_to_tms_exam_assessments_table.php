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
        Schema::table('tms_exam_assessments', function (Blueprint $table) {
            $table->time('assessment_time')->nullable()->after('title');
            $table->time('assessment_duration')->nullable()->after('assessment_time');
            $table->tinyInteger('date_option')->after('type')->default(1)->comment('start_date - 1, end_date - 2');

        });
        \DB::statement('UPDATE tms_exam_assessments, tms_exams SET tms_exam_assessments.assessment_time = tms_exams.exam_time,  tms_exam_assessments.assessment_duration = tms_exams.exam_duration WHERE tms_exam_assessments.exam_id = tms_exams.id');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tms_exam_assessments', function (Blueprint $table) {
            $table->dropColumn('assessment_time');
            $table->dropColumn('assessment_duration');
        });
    }
};
