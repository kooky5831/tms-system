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
        Schema::table('grants', function (Blueprint $table) {
            $table->date('disbursement_date')->nullable()->after('amount_recovery');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grants', function (Blueprint $table) {
            $table->dropColumn('disbursement_date');
        });
    }
};
