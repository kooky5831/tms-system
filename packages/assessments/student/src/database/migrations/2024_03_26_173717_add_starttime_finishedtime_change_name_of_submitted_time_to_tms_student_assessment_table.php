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
            $table->dropColumn('assessment_submitted_time');
            $table->timestamp('finished_time')->nullable()->after('is_finished');
            $table->timestamp('started_time')->nullable()->after('is_started');
            $table->timestamp('reviewed_time')->nullable()->after('is_reviewed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tms_student_assessment', function (Blueprint $table) {
            $table->dropColumn('finished_time');
            $table->dropColumn('started_time');
            $table->dropColumn('reviewed_time');
        });
    }
};
