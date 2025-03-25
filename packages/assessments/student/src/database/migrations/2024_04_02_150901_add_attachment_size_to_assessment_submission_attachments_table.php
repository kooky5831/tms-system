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
        Schema::table('assessment_submission_attachments', function (Blueprint $table) {
            $table->string('attachment_size')->nullable()->after('submission_attchment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessment_submission_attachments', function (Blueprint $table) {
            $table->dropColumn('attachment_size');
        });
    }
};
