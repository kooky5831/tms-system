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
        Schema::table('course_mains', function (Blueprint $table) {
            $table->tinyInteger('application_fees')->after('gst_applied_on')->default(0)->comment('0 => Not, 1 => Yes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_mains', function (Blueprint $table) {
            $table->dropColumn('application_fees');
        });
    }
};
