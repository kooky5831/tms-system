<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseMainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_mains', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('course_type_id')->unsigned();
            $table->string('branding_theme_id', 100)->nullable();
            $table->string('name');
            $table->string('reference_number');
            $table->string('course_mode_training')->nullable();
            $table->string('skill_code')->nullable();
            $table->decimal('course_full_fees', 8, 2)->default(888);
            $table->tinyInteger('course_type')->default(1)->comment('1-WQS,2-non-WQS');
            $table->string('single_course_ids')->comment('for modular course')->nullable();
            $table->string('certificate_file')->nullable();
            $table->text('cert_cordinates')->nullable();
            $table->text('shared_drive_id')->nullable();
            $table->text('attendance_file_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->softDeletes();
            $table->index(['deleted_at']);

            $table->foreign('course_type_id')->references('id')->on('course_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_mains');
    }
}
