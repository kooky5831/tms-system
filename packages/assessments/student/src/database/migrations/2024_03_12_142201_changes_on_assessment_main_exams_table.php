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
        //
        Schema::table('assessment_main_exams', function (Blueprint $table) {
            $table->dropIndex('assessment_main_exams_deleted_at_index');
            $table->dropColumn('assessment_type');
            $table->dropColumn('assessment_name');
            $table->dropColumn('exam_date');
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
        });

        Schema::rename('assessment_main_exams', 'tms_exams');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('tms_exams', function (Blueprint $table) {
            $table->string('assessment_type');
            $table->string('assessment_name');
            $table->date('exam_date');
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
        });

        Schema::rename('tms_exams', 'assessment_main_exams');

        Schema::table('assessment_main_exams', function (Blueprint $table) {
            $table->index(['deleted_at']);
        });
    }
};
