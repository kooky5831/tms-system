<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entry_id')->unsigned()->nullable();
            $table->string('xero_pay_id')->unique()->nullable();
            $table->unsignedBigInteger('student_enrolments_id')->unsigned();
            $table->string('payment_mode');
            $table->string('creditcard_number')->nullable();
            $table->string('creditcard_type')->nullable();
            $table->string('ip_address')->nullable();
            /*$table->string('payment_amount');*/
            $table->string('payment_date');
            $table->string('payment_method')->nullable();
           /* $table->string('payment_status');*/

            $table->string('cheque_no')->nullable();
            $table->string('account_number')->nullable();

            $table->string('fee_amount')->nullable();
            /*$table->string('fee_status')->nullable();*/
            $table->string('transaction_id');
            $table->string('transaction_type')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0-paid,1-cancelled');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->softDeletes();
            $table->index(['deleted_at']);

            $table->foreign('student_enrolments_id')->references('id')->on('student_enrolments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
