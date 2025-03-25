<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateErrorExceptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('error_exceptions', function (Blueprint $table) {
            $table->id();
            $table->dateTime('datetime')->nullable();
            $table->text('name')->nullable();
            $table->text('code')->nullable();
            $table->text('filepath')->nullable();
            $table->longText('message')->nullable();
            $table->longText('trace')->nullable();
            $table->tinyInteger('status')->comment('1-Pending,2-Resolved')->default(1);
            $table->longText('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('error_exceptions');
    }
}
