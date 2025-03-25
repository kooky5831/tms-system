<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('courserun_id')->index();
            $table->foreign('courserun_id')->references('id')->on('courses')->onDelete('cascade');
            $table->unsignedBigInteger('student_enroll_id')->index();
            $table->foreign('student_enroll_id')->references('id')->on('student_enrolments')->onDelete('cascade')->nullable();
            $table->tinyInteger('is_comapany')->default(0)->comment('student - 0, comapany - 1');
            $table->tinyInteger('xero_sync')->default(0)->comment('false - 0, true - 1');
            $table->string('invoice_name');
            $table->string('invoice_number')->nullable();
            $table->string('xero_invoice_id')->nullable();
            $table->string('invoice_type')->nullable();
            $table->string('invoice_status')->nullable();
            $table->double('amount_due');
            $table->double('amount_paid');
            $table->double('sub_total');
            $table->double('tax');
            $table->double('total_discount');
            $table->longText('line_items');
            $table->date('invoice_date');
            $table->date('due_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
