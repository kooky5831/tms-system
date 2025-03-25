<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class AddPaymentRemarkDueDateCoulmnOnStudentEnrolmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('student_enrolments', function (Blueprint $table) {
            $table->longText('payment_remark')->after('tgp_payment_response')->nullable();
            $table->date('due_date')->after('payment_remark')->nullable();
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        $table->dropColumn('payment_remark');
        $table->dropColumn('due_date');
    }
}