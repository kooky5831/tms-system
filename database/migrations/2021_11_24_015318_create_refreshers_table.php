<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefreshersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refreshers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id')->unsigned();
            $table->unsignedBigInteger('student_id')->unsigned();

            $table->tinyInteger('isAttendanceRequired')->default(0)->comment('Yes-1,No-0');
            $table->tinyInteger('isAssessmentRequired')->default(0)->comment('Yes-1,No-0');
            $table->tinyInteger('isAttendanceError')->nullable()->default(NULL)->comment('Yes-1,No-0');
            $table->tinyInteger('isAssessmentError')->nullable()->default(NULL)->comment('Yes-1,No-0');
            $table->json('attendance')->nullable();
            $table->string('assessment')->nullable()->comment('c,nyc');
            $table->text('assessment_remark')->nullable();
            $table->date('assessment_date')->nullable();
            $table->text('notes')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0-Pending,1-Accepted,2-Cancelled');
            $table->longText('attendanceResponse')->nullable();
            $table->longText('assessmentResponse')->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->softDeletes();
            $table->index(['deleted_at']);

            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('refreshers');
    }
}
