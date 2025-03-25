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
            $table->string('gform_id')->nullable()->after('course_brochure_determined');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_enrolments', function (Blueprint $table) {
            $table->dropColumn('gform_id');
        });
    }
};
