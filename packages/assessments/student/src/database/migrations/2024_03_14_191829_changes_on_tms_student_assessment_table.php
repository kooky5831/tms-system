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
        Schema::table('tms_student_assessment', function (Blueprint $table) {
            $table->renameColumn('assessment_id', 'assessment_run_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('tms_student_assessment', function (Blueprint $table) {
            $table->renameColumn('assessment_run_id', 'assessment_id');
        });
    }
};
