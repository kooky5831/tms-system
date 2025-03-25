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
            //
            $table->tinyInteger('gst_applied_on')->after('gst')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_mains', function (Blueprint $table) {
            //
            $table->dropColumn('gst_applied_on');
        });
    }
};
