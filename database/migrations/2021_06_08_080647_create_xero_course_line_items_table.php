<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateXeroCourseLineItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xero_course_line_items', function (Blueprint $table) {
            $table->unsignedBigInteger('course_main_id')->unsigned();
            $table->string('code');
            $table->string('account_code');
            $table->string('name');
            $table->text('description');
            $table->string('amount');

            $table->foreign('course_main_id')->references('id')->on('course_mains')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('xero_course_line_items');
    }
}
