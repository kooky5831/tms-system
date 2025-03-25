<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->longText('description');
            $table->string('subject');
            $table->string('slug');
            $table->tinyInteger('template_for')->comment('1 => Admin, 2 => Course')->nullable();
            $table->longText('template_text');
            $table->longText('keywords');
            $table->tinyInteger('status')->comment('0 => InActive, 1 => Active')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_templates');
    }
}
