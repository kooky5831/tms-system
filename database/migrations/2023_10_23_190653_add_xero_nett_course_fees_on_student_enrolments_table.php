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
        Schema::table('student_enrolments', function (Blueprint $table) {
            //
            $table->decimal('xero_nett_course_fees', $precision = 8, $scale = 2)->after('xero_due_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_enrolments', function (Blueprint $table) {
            //
            $table->dropColumn('xero_nett_course_fees');
        });
    }
};
