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
        Schema::table('student_enrolments', function (Blueprint $table) {
            $table->string('application_fee')->after('is_feedback_submitted')->nullable();
            $table->string('payment_type')->after('application_fee')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('student_enrolments', function (Blueprint $table) {
            $table->dropColumn('application_fees');
            $table->dropColumn('payment_type');
        });
    }
};
