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
        Schema::table('tms_exam_assement_course_runs', function (Blueprint $table) {
            //
            $table->timestamp('started_at')->after('is_assigned')->nullable();
            $table->timestamp('ended_at')->after('started_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tms_exam_assement_course_runs', function (Blueprint $table) {
            //
            $table->dropColumn('started_at');
            $table->dropColumn('ended_at');
        });
    }
};
