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
        Schema::create('course_resources_coursemains', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_main_id')->unsigned();
            $table->unsignedBigInteger('course_resource_id')->unsigned();
            $table->timestamp('created_at')->useCurrent()->nullable();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->softDeletes();
            $table->index(['deleted_at']);
            
            $table->foreign('course_main_id')->references('id')->on('course_mains')->onDelete('cascade');
            $table->foreign('course_resource_id')->references('id')->on('course_resources')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_resources_coursemains');
    }
};
