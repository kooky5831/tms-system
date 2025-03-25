<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTpgPaymentFieldsToStudentEnrolmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('student_enrolments', function (Blueprint $table) {
            $table->tinyInteger('isPaymentError')->nullable()->default(NULL)->after('isAssessmentError')->comment('Yes-1,No-0');
            $table->tinyInteger('tpg_payment_sync')->default(NULL)->nullable()->after('assessment_sync')->comment('Sync-1,Notsync-2');
            $table->longText('tgp_payment_response')->default(NULL)->nullable()->after('assessmentResponse');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_enrolments', function (Blueprint $table) {
            $table->dropColumn('isPaymentError');
            $table->dropColumn('tpg_payment_sync');
            $table->dropColumn('tgp_payment_response');
        });
    }
}
