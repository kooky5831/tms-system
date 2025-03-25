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
        Schema::table('tms_exams', function (Blueprint $table) {
            $table->dropColumn('exam_duration');
            $table->dropColumn('exam_time');

            $table->string('main_exam')->after('id')->nullable()->default('tms_exam_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tms_exams', function (Blueprint $table) {
            $table->dropColumn('main_exam');
        });
    }
};
