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
        Schema::create('xero_branding_themes', function (Blueprint $table) {
            $table->id();
            $table->string('branding_theme_id')->nullable();
            $table->string('name')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('type')->nullable();
            $table->integer('sort_order')->nullable();
            $table->text('created_date_utc')->nullable();
            $table->text('applied_on')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('xero_branding_themes');
    }
};
