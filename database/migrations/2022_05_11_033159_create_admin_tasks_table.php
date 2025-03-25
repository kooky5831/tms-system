<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('course_id')->unsigned()->nullable();
            $table->unsignedBigInteger('sms_template_id')->unsigned()->nullable();
            $table->integer('task_type')->default(1)->comment('1-email,2-SMS,3-TextTask');
            $table->text('template_name');
            $table->text('template_slug');
            $table->text('task_text');
            $table->text('notes')->nullable();
            $table->tinyInteger('status')->default(1)->comment('Created-1,Pending-2,Completed-3');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->integer('completed_by')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->softDeletes();

            $table->index(['deleted_at']);

            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
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
        Schema::dropIfExists('admin_tasks');
    }
}
