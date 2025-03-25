<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('student_enrolments', function (Blueprint $table) {
            $table->unsignedBigInteger('program_type_id')->unsigned()->nullable()->after('payment_type');
            $table->tinyInteger('is_invoice_generated')->after('program_type_id')->comment('0 => No, 1 => Yes')->default(0);
            
            $table->foreign('program_type_id')->references('id')->on('program_types')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_enrolments', function (Blueprint $table) {
            $table->dropColumn('program_type_id');
            $table->dropColumn('is_invoice_generated');
        });
    }
};
