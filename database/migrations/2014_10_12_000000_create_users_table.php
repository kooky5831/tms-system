<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone_number')->nullable();
            $table->string('address')->nullable();
            $table->enum('role', ['superadmin','staff','trainer'])->default('trainer');
            $table->tinyInteger('status')->default(1)->comment('Active - 1, Inactive - 0');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('timezone')->default('Asia/Singapore');
            $table->integer('gender')->default(1)->nullable()->comment('1: Male, 2: Female,3: Other');
            $table->date('dob')->nullable();
            $table->string('profile_avatar', 255)->default('default.jpg');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
