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
        Schema::table('grant_logs', function (Blueprint $table) {
            $table->tinyInteger('grant_notify')->default(0)->comment('Yes - 1, No - 0')->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grant_logs', function (Blueprint $table) {
            $table->dropColumn('grant_notify');
        });
    }
};
