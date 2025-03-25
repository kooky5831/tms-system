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
        Schema::create('tms_exam_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_id')->index()->nullable();
            $table->foreign('exam_id')->references('id')->on('tms_exams')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->string('type')->nullable();
            $table->timestamps();
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_assessments');
    }
};
