<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseRunTriggersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_run_triggers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('course_main_id')->index();
            $table->unsignedBigInteger('sms_template_id')->index()->nullable();
            $table->string('triggerTitle');
            $table->integer('group_no')->nullable();
            $table->integer('event_when')->default(1);
            $table->integer('event_type')->default(1)->comment('1-email,2-SMS,3-TextTask');
            $table->integer('no_of_days')->default(1)->comment('days before this event should trigger');
            $table->integer('date_in_month')->comment('date of event every month');
            $table->integer('day_of_week')->default(1);
            $table->text('template_name');
            $table->text('template_slug');
            $table->text('task_text');
            $table->tinyInteger('status')->default(1)->comment('Active - 1, Inactive - 0');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->softDeletes();

            $table->index(['deleted_at']);

            $table->foreign('course_main_id')->references('id')->on('course_mains')->onDelete('cascade');
            $table->foreign('sms_template_id')->references('id')->on('sms_templates')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_run_triggers');
    }
}
