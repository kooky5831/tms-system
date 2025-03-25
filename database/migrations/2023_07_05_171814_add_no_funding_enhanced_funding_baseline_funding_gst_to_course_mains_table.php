<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_mains', function (Blueprint $table) {
            //
            $table->bigInteger('no_funding')->after('attendance_file_id')->default(0)->nullable();
            $table->bigInteger('enhanced_funding')->after('no_funding')->nullable();
            $table->bigInteger('baseline_funding')->after('enhanced_funding')->nullable();
            $table->bigInteger('gst')->after('baseline_funding')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_mains', function (Blueprint $table) {
            //
            $table->dropColumn('no_funding');
            $table->dropColumn('enhanced_funding');
            $table->dropColumn('baseline_funding');
            $table->dropColumn('gst');
        });
    }
};
