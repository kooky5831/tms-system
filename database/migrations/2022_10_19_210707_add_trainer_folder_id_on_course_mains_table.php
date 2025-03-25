<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTrainerFolderIdOnCourseMainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_mains', function (Blueprint $table) {
            $table->text('trainer_folder_id')->after('shared_drive_id')->nullable();
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
            $table->dropColumn('trainer_folder_id');
        });
    }
}
