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
            $table->tinyInteger('is_discount')->after('is_absorb_gst')->comment('0 => No, 1 => Yes')->default(0);
            $table->string('discount_amount')->after('is_discount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_mains', function (Blueprint $table) {
            $table->dropColumn('is_discount');
            $table->dropColumn('discount_amount');
        });
    }
};
