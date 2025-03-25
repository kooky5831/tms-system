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
            $table->tinyInteger('is_discount')->after('name')->comment('0 => No, 1 => Yes')->default(0);
            $table->string('discount_percentage')->after('is_discount')->nullable();
            $table->tinyInteger('is_application_fee')->after('discount_percentage')->comment('0 => No, 1 => Yes')->default(0);
            $table->string('application_fee')->after('is_application_fee')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('program_types', function (Blueprint $table) {
            $table->dropColumn('is_discount');
            $table->dropColumn('discount_percentage');
            $table->dropColumn('is_application_fee');
            $table->dropColumn('application_fee');
        });
    }
};
