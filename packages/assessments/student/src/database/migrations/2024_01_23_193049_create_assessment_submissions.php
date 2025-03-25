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
        Schema::create('assessment_submissions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('question_id');
            $table->bigInteger('exam_id');
            $table->bigInteger('student_enr_id');
            $table->longText('submitted_answer');
            $table->string('is_pass');
            $table->tinyInteger('is_reviewed');
            $table->string('assessment_type');
            $table->integer('edited_count');
            $table->string('remarks');
            $table->string('answer_image');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->softDeletes();

            $table->index(['deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_submissions');
    }
};
