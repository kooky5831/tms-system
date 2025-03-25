<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseSoftBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_soft_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id')->unsigned();
            $table->string('name');
            $table->string('nric');
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();
            $table->text('notes')->nullable();
            $table->date('deadline_date');
            $table->tinyInteger('status')->default(0)->comment('Booked-1, Pending-0');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->softDeletes();
            $table->index(['deleted_at']);

            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_soft_bookings');
    }
}
