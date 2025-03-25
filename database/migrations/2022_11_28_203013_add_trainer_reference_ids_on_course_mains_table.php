<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTrainerReferenceIdsOnCourseMainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_mains', function (Blueprint $table) {
            $table->text('doc_file_id')->after('trainer_folder_id')->nullable();
            $table->text('spreadsheet_file_id')->after('doc_file_id')->nullable();
            $table->text('assessment_file_id')->after('spreadsheet_file_id')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
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
            $table->dropColumn('doc_file_id');
            $table->dropColumn('spreadsheet_file_id');
            $table->dropColumn('assessment_file_id');
            $table->dropColumn('start_time');
            $table->dropColumn('end_time');
        });
    }
}
