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
        Schema::table('program_types', function (Blueprint $table) {
            $table->tinyInteger('is_absorb_gst')->after('application_fee')->comment('0 => No, 1 => Yes')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('program_types', function (Blueprint $table) {
            $table->dropColumn('is_absorb_gst');
        });
    }
};
