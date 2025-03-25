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
            $table->decimal('xero_amount', $precision = 8, $scale = 2)->after('xero_invoice_number')->nullable();
            $table->decimal('xero_paid_amount', $precision = 8, $scale = 2)->after('xero_amount')->nullable();
            $table->decimal('xero_due_amount', $precision = 8, $scale = 2)->after('xero_paid_amount')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_enrolments', function (Blueprint $table) {
            //
            $table->dropColumn('xero_amount');
            $table->dropColumn('xero_due_amount');
            $table->dropColumn('xero_paid_amount');
        });
    }
};
