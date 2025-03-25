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
        //
        Schema::dropIfExists('revisions');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::create('revisions', function ($table) {
            $table->bigIncrements('id');
            $table->string('revisionable_type');
            $table->unsignedBigInteger('revisionable_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('key');
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->timestamps();

            $table->index(array('revisionable_id', 'revisionable_type'));
        });
    }
};
