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
        Schema::create('admin_tasks_mail_logs', function (Blueprint $table) {
            $table->id();
            $table->string('mail_logs_subject')->nullable();
            $table->string('mail_logs_from')->nullable();
            $table->string('mail_logs_to')->nullable();
            $table->string('mail_logs_cc')->nullable();
            $table->string('mail_logs_bcc')->nullable();
            $table->text('mail_logs_content')->nullable();
            $table->time('mail_logs_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_tasks_mail_logs');
    }
};
