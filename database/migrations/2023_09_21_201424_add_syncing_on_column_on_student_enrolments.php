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
            $table->tinyInteger('master_invoice')->after('xero_invoice_number')->default(1)->comment('tms - 1, xero - 0');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_enrolments', function (Blueprint $table) {
            //
            $table->dropColumn('master_invoice');
        });
    }
};
