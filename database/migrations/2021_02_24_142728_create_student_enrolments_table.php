<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentEnrolmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_enrolments', function (Blueprint $table) {
            $table->id();
            $table->string('xero_invoice_id')->unique()->nullable();
            $table->unsignedBigInteger('course_id')->unsigned();
            $table->unsignedBigInteger('student_id')->unsigned();
            $table->string('tpgateway_refno')->unique()->nullable();

            $table->string('sponsored_by_company')->nullable();
            $table->string('xero_invoice_number')->nullable();
            $table->string('company_sme')->nullable();
            $table->string('nationality')->nullable();
            $table->string('age')->nullable();
            $table->string('learning_mode')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('dob')->nullable();
            $table->string('education_qualification')->nullable();
            $table->string('designation')->nullable();
            $table->string('salary')->nullable();
            $table->string('company_name')->nullable();
            $table->string('company_uen')->nullable();
            $table->string('company_contact_person')->nullable();
            $table->string('company_contact_person_email')->nullable();
            $table->string('company_contact_person_number')->nullable();
            $table->string('billing_email')->nullable();
            $table->string('billing_address')->nullable();
            $table->string('billing_zip')->nullable();
            $table->string('billing_country')->nullable();
            $table->longText('remarks')->nullable();
            $table->string('payment_mode_company')->nullable();
            $table->string('payment_mode_individual')->nullable();
            $table->string('other_paying_by')->nullable();
            /*$table->string('payment_mode')->nullable();*/
            $table->decimal('amount', 8, 2)->default(0);
            $table->decimal('discountAmount', 8, 2)->default(0);
            $table->integer('payment_tpg_status')->default(1)->comment('1-Pending,2-Partial,3-Full,4-Refund');
            $table->integer('payment_status')->default(1)->comment('1-Pending,2-Partial,3-Full,4-Refund');
            $table->tinyInteger('status')->default(3)->comment('Enrolled - 0, Cancelled - 1, Hold - 2, Not Enrolled - 3');
            $table->string('meal_restrictions')->nullable();
            $table->string('meal_restrictions_type')->nullable();
            $table->string('meal_restrictions_other')->nullable();
            $table->string('computer_navigation_skill')->nullable();
            $table->string('course_brochure_determined')->nullable();
            $table->string('entry_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->decimal('amount_paid', 8, 2)->default(0);
            $table->tinyInteger('enrollToTPG')->default(0)->comment('Yes-1, No-0, 2-Cancel');
            $table->tinyInteger('isGrantError')->nullable()->default(NULL)->comment('Yes-1,No-0');
            $table->string('grantEstimated')->nullable();
            $table->string('grantRefNo')->nullable();
            $table->string('grantStatus')->nullable();
            $table->tinyInteger('isAttendanceError')->nullable()->default(NULL)->comment('Yes-1,No-0');
            $table->tinyInteger('isAssessmentError')->nullable()->default(NULL)->comment('Yes-1,No-0');
            $table->json('attendance')->nullable();
            $table->string('assessment')->nullable()->comment('c,nyc');
            $table->text('assessment_remark')->nullable();
            $table->date('assessment_date')->nullable();
            $table->longText('enrollmentResponse')->nullable();
            $table->longText('grantResponse')->nullable();
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
        Schema::dropIfExists('student_enrolments');
    }
}
