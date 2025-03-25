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
        Schema::table('tms_student_assessment', function (Blueprint $table) {
            $table->time('exam_duration')->nullable()->after('started_time');
            $table->time('assessment_duration')->nullable()->after('exam_duration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tms_student_assessment', function (Blueprint $table) {
            $table->dropColumn('assessment_duration');
            $table->dropColumn('exam_duration');
        });
    }
};
