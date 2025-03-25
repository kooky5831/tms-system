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
        Schema::table('student_enrolments', function (Blueprint $table) {
            $table->dropColumn('enrollToTPG');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_enrolments', function (Blueprint $table) {
            $table->tinyInteger('enrollToTPG')->default(0)->comment('Yes-1, No-0, 2-Cancel');
        });
    }
};
