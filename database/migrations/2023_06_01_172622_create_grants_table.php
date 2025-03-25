<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGrantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_enrolment_id')->unsigned();
            $table->string('grant_refno')->nullable();
            $table->string('grant_status')->nullable();
            $table->string('scheme_code')->nullable();
            $table->string('scheme_description')->nullable();
            $table->string('component_code')->nullable();
            $table->string('component_description')->nullable();
            $table->decimal('amount_estimated', 8, 2)->default(0);
            $table->decimal('amount_paid', 8, 2)->default(0);
            $table->decimal('amount_recovery', 8, 2)->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->softDeletes();
            $table->index(['deleted_at']);
            
        });

        Schema::table('grants', function(Blueprint $table)
        {
            $table->foreign('student_enrolment_id')->references('id')->on('student_enrolments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grants');
    }
}
