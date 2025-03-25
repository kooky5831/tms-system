<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('xero_id')->unique()->nullable();
            $table->string('name');
            $table->string('nric')->unique();

            $table->string('nationality')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('dob')->nullable();
            $table->string('company_sme')->nullable();
            $table->string('company_name')->nullable();
            $table->string('company_uen')->nullable();
            $table->string('company_contact_person')->nullable();
            $table->string('company_contact_person_email')->nullable();
            $table->string('company_contact_person_number')->nullable();
            $table->string('billing_address')->nullable();
            $table->string('meal_restrictions')->nullable();
            $table->string('meal_restrictions_type')->nullable();
            $table->string('meal_restrictions_other')->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->softDeletes();

            $table->index(['deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
}
