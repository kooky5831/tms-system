<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAssessmentShortTitleOnCourseMainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_mains', function (Blueprint $table) {
            $table->text('assessment_short_title')->after('assessment_file_id')->nullable();
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
            $table->dropColumn('assessment_short_title');
        });
    }
}
