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
            $table->string('pesa_refrerance_number')->after('remarks')->nullable();
            $table->string('skillfuture_credit')->after('pesa_refrerance_number')->nullable();
            $table->string('vendor_gov')->after('skillfuture_credit')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_enrolments', function (Blueprint $table) {
            //
            $table->dropColumn('pesa_refrerance_number');
            $table->dropColumn('skillfuture_credit');
            $table->dropColumn('vendor_gov');
        });
    }
};
