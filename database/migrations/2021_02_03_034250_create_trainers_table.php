<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trainers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unsigned();
            $table->string('tpgateway_id')->unique()->nullable();
            $table->tinyInteger('type')->comment('1-existing,2-new')->default(2);
            $table->text('experience')->nullable();
            $table->string('linkedInURL')->nullable();
            $table->integer('salutationId')->comment('1-Mr,2-Ms,3-Mdm,4-Mrs,5-Dr,6-Prof');
            $table->json('qualifications')->nullable();
            $table->longText('domainAreaOfPractice')->nullable();
            $table->longText('tpgResponse')->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->softDeletes();

            $table->index(['deleted_at']);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trainers');
    }
}
