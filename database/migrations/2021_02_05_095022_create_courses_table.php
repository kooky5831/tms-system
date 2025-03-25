<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('venue_id')->index();
            $table->unsignedBigInteger('maintrainer')->index(); // user_id
            $table->string('tpgateway_id')->unique()->nullable();

            $table->date('registration_opening_date');
            $table->date('registration_closing_date');
            $table->time('registration_closing_time')->default('18:00:00');
            $table->date('course_start_date');
            $table->date('course_end_date');
            $table->text('course_link')->nullable();
            $table->string('meeting_id')->nullable();
            $table->string('meeting_pwd')->nullable();
            $table->string('schinfotype_code')->default(1);
            $table->string('schinfotype_desc')->nullable();
            $table->text('sch_info');
            $table->integer('minintakesize')->default(0)->nullable();
            $table->integer('intakesize')->default(0)->nullable();
            $table->integer('threshold')->default(0)->nullable();
            $table->integer('modeoftraining');
            $table->integer('registeredusercount')->default(0)->nullable();
            $table->integer('cancelusercount')->default(0)->nullable();
            $table->string('coursevacancy_code')->default('L')->nullable();
            $table->string('coursevacancy_desc')->nullable();
            $table->string('course_remarks')->nullable();
            $table->text('coursefileimage')->nullable();
            $table->tinyInteger('is_published')->default(0)->comment('Published-1,Un Published-0,Cancelled-2');
            $table->longText('courseRunResponse')->nullable();
            $table->tinyInteger('isAttendanceSubmitedTPG')->default(0)->comment('Yes-1,No-0,Error-2');
            $table->tinyInteger('isAssessmentSubmitedTPG')->default(0)->comment('Yes-1,No-0,Error-2');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->softDeletes();

            $table->index(['deleted_at']);

            $table->foreign('venue_id')->references('id')->on('venues')->onDelete('cascade');
            $table->foreign('maintrainer')->references('id')->on('trainers')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('courses');
    }
}
