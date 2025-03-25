<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAssessmentSyncToStudentEnrolments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('student_enrolments', function (Blueprint $table) {
            $table->tinyInteger('assessment_sync')->default(NULL)->nullable()->after('assessment')->comment('Sync-1,Notsync-2');
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
            $table->dropColumn('assessment_sync');
        });
    }
}
