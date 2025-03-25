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
            $table->tinyInteger('trainee_view_access')->after('date_option')->default(0)->comment('0 - Not , 1 - Yes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tms_exam_assessments', function (Blueprint $table) {
            $table->dropColumn('trainee_view_access');
        });
    }
};
