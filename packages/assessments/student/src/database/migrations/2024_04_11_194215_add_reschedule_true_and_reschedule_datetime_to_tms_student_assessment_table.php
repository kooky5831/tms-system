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
            $table->tinyInteger('is_reschedule')->nullable()->after('assessment_duration')->default(0)->comment('0 - Not, 1 - Yes');
            $table->timestamp('is_reschedule_time')->nullable()->after('is_reschedule');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tms_student_assessment', function (Blueprint $table) {
            $table->dropColumn('is_reschedule');
            $table->dropColumn('is_reschedule_time');
        });
    }
};
