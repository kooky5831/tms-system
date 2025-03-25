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
            $table->tinyInteger('is_grant_active')->after('attendance_file_id')->default(1)->comment('inactive - 0, active - 1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_mains', function (Blueprint $table) {
            //
            $table->dropColumn('is_grant_active');
        });
    }
};
