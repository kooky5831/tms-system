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
        Schema::table('tms_student_results', function (Blueprint $table) {
            $table->text('assessment_recovery')->nullable()->after('is_passed');
            $table->dropColumn('assement_remarks');
            $table->text('assessment_reschedule_note')->nullable()->after('assessment_recovery');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tms_student_results', function (Blueprint $table) {
            $table->dropColumn('assessment_recovery');
            $table->dropColumn('assessment_reschedule_note');
        });
    }
};
